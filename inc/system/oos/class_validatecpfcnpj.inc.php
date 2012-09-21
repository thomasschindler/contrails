<?php
/**
 * Returned if the number has unexpected invalid chars.
 */
define('VALIDATE_INVALID_CHARS',      -1);
/**
 * Returned if the number has a invalid lenght.
 */
define('VALIDATE_INVALID_NUMBER_LEN', -2);
/**
 * Returned if the number has a suspect sequence of chars.
 */
define('VALIDATE_INVALID_NUMBER_SEQ', -3);
/**
 * Returned if the number is not set.
 */
define('VALIDATE_NUMBER_NOT_SET',     -4);
/**
 * ValidateCpfCnpj
 *
 * The validateCpfCnpj class provides methods
 * to validate Cadastro de Pessoas Físicas (CPF)
 * and Cadastro de Pessoas Jurídicas (CNPJ) brazilian
 * numbers.
 *
 * @category   Validation
 * @author     Mario Arroyo <marioarroyo.map@gmail.com>
 * @license    
 * @version    Release: 1.0
 * @link       
 */
class validatecpfcnpj
{
	// {{{ properties
	
	/**
     * The number to be validate
     *
     * If this variable is empty, a call to validate()
	 * will return an error code.
     *
     * @var  string
     * @see  setNumber()
     */
	private $number = '';
	
	/**
     * Determines if the number is a CNPJ number
     *
     * @var  bool
     * @see  setNumber(), validate()
     */
	private $isCnpj = false;
	
	/**
     * Determines if the number is a CPF number
     *
     * @var  bool
     * @see  setNumber(), validate()
     */
	private $isCpf  = false;
	
	/**
     * Determines de error generated during the execution
     *
     * @var  int
     */
	 private $errorCode = VALIDATE_NUMBER_NOT_SET;
	
	// }}}
	// {{{ cleanNumber()
	
	/**
      * Clean the number
      *
      * Remove all '.', '-', '/' and blank spaces from the
      * number.
      *
      * @param reference A reference to the var to be clean.
	  * @return void
      * @access private
      */
	private function cleanNumber(&$number)
	{
		$number = trim($number);
		
		$number = str_replace('.', '', $number);
		$number = str_replace('-', '', $number);
		$number = str_replace('/', '', $number);
	}
	
	// }}}
	// {{{ checkNumberSequence()
	
	/**
      * Check for a valid number
      *
      * Check if the number is not formed for
      * sequence of 1, 2, 3, 4, 5, 6, 7, 8 or 9
	  * like 00000000000 or 44444444444
      *
      * @param int The number to be checked.
	  * @return void
      * @access private
      */
	private function checkNumberSequence($number)
	{
		for($i = 0; $i <= 9; $i++)
		{
			if(strcmp($number, str_pad('', strlen($number), $i)) === 0)
			{
				return false;	
			}
		}
		
		return true;
	}
	
	// }}}
	// {{{ setNumber()
	
	/**
      * Sets the number to be verifyed
      *
      * @param int The number to be set.
	  * @return void
      * @access public
      */
	public function setNumber($number)
	{
		// Clean the number.
		$this->cleanNumber($number);
		
		// Validates if the number is a real integer (prevent
		// the insert of unexpected characters such as '*')
		if(filter_var((int)$number, FILTER_VALIDATE_INT))
		{
			// If the number lenght is equal to 14 then
			// the number is a CNPJ number
			if(strlen($number) == 14)
			{
				$this->isCnpj = true;	
			}
			// If the number lenght is equal to 11 then
			// the number is a CPF number
			else if(strlen($number) == 11)
			{
				$this->isCpf = true;	
			}
			else
			{
				//Invalid lenght
				$this->errorCode = VALIDATE_INVALID_NUMBER_LEN;	
			}
			
			if($this->isCnpj || $this->isCpf)
			{
				// Check if the number if not formed for a
				// sequence of one number such as 00000000000.
				if($this->checkNumberSequence($number))
				{
					// Set the number
					$this->number = $number;	
				}
			}
			else
			{
				// Invalid number sequence
				$this->errorCode = VALIDATE_INVALID_NUMBER_SEQ;	
			}
		}
		else
		{
			// Has unexpected chars
			$this->errorCode = VALIDATE_INVALID_CHARS;	
		}
	}
	
	// }}}
	// {{{ validateCnpj()
	
	/**
      * Validate the number as a CNPJ number
      *
	  * Uses the CNPJ validation algorithm to
	  * verify if the number is valid
	  *
	  * More info http://pt.wikipedia.org/wiki/Cnpj
	  *
	  * @return bool
      * @access private
      */
	private function validateCnpj()
	{
		$verify   = array('firstDigit'  => 0,
	                      'secondDigit' => 0,
		                 );
		
		$multiple = 5;
		
		for($i = 0; $i < 12; $i++)
		{
			$verify['firstDigit'] += ($multiple * (int)$this->number[$i]);
			
			if($multiple-- == 2)
			{
				$multiple = 9;	
			}
		}
		
		$verify['firstDigit'] = 11 - ($verify['firstDigit'] % 11);
		
		if($verify['firstDigit'] >= 10)
		{
			$verify['firstDigit'] = 0;	
		}
		
		$multiple = 6;
		
		for($i = 0; $i < 12; $i++)
		{
			$verify['secondDigit'] += ($multiple * (int)$this->number[$i]);
			
			if($multiple-- == 2)
			{
				$multiple = 9;	
			}
		}
		
		$verify['secondDigit'] += (2 * $verify['firstDigit']);
		
		$verify['secondDigit']  = 11 - ($verify['secondDigit'] % 11);
		
		if($verify['secondDigit'] >= 10)
		{
			$verify['secondDigit'] = 0;	
		}
		
		$digits  = substr($this->number, (strlen($this->number) - 2), 2);
		
		if(strcmp("{$verify['firstDigit']}{$verify['secondDigit']}", $digits) === 0)
		{
			return true;	
		}
		
		return false;
	}
	
	// }}}
	// {{{ validateCpf()
	/**
      * Validate the number as a CPF number
      *
	  * Uses the CPF validation algorithm to
	  * verify if the number is valid
	  *
	  * More info http://en.wikipedia.org/wiki/Cadastro_de_Pessoas_F%C3%ADsicas
	  *
	  * @return bool
      * @access private
      */
	private function validateCpf()
	{
		$verify   = array('firstDigit'  => 0,
		                  'secondDigit' => 0,
				 );
		
		$multiple = 10;
		
		for($i = 0; $i < 9; $i++)
		{
			$verify['firstDigit'] += ($multiple-- * (int)$this->number[$i]);
		}
		
		$verify['firstDigit'] = 11 - ($verify['firstDigit'] % 11);
		
		if($verify['firstDigit'] >= 10)
		{
			$verify['firstDigit'] = 0;	
		}
		
		$multiple = 11;
		
		for($i = 0; $i < 9; $i++)
		{
			$verify['secondDigit'] += ($multiple-- * (int)$this->number[$i]);	
		}
		
		$verify['secondDigit'] += (2 * $verify['firstDigit']);
		
		$verify['secondDigit'] = 11 - ($verify['secondDigit'] % 11);
		
		if($verify['secondDigit'] >= 10)
		{
			$verify['secondDigit'] = 0;	
		}
		
		$digits = substr($this->number, (strlen($this->number) - 2), 2);
		
		if(strcmp("{$verify['firstDigit']}{$verify['secondDigit']}", $digits) === 0)
		{
			return true;	
		}
		
		return false;
	}
	
	// }}}
	// {{{ validate()
	/**
      * Call the correct validation function
      *
	  * Returns the value of the the correct
	  * validation method
	  *
	  * @return bool, int
      * @access public
      */
	public function validate()
	{
		if(!empty($this->number))
		{
			if($this->isCnpj)
			{
				return $this->validateCnpj();
			}
			else if($this->isCpf)
			{
				return $this->validateCpf();	
			}
		}
		else
		{
			return $this->errorCode;	
		}
	}
	
	// }}}
}
?>
