<?php
//
// +----------------------------------------------------------------------+
// | NestedSet                                                            |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 Arne Klempert                                     |
// +----------------------------------------------------------------------+
// | License (LGPL)                                                       |
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// +----------------------------------------------------------------------+
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     |
// | Lesser General Public License for more details.                      |
// +----------------------------------------------------------------------+
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation Inc., 59 Temple Place,Suite 330, Boston,MA 02111-1307 USA |
// +----------------------------------------------------------------------+
// | Author: Arne Klempert <arne@klempert.de>                             |
// +----------------------------------------------------------------------+
//

/**
* NestedSet
*
* This class handles all the stuff needed for databases
* using the "nested set" model, which allows to select
* all nodes of a tree without any recursive calls.
* Espesially usefull when using a RDBMS not supporting
* this feature by it self (for example MySQL).
*
* !!! IMPORTANT !!!
* This class is in alpha stadium.
* The API of this class may change.
* Please send comments and bug reports to <arne@klempert.de>
*
* @version 0.6
* @author   Arne Klempert <arne@klempert.de>
* @see http://www.klempert.de/
*
*   modified by:
*
*   @author         hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*   @date           12 05 2004
*   @version        1.0
*   @since          1.0
*   @access         public
*   @package        util
*/
class NestedSet 
{

    /**
    * zeiger auf globals DB objekt
    *
    *@var    object
    */
    var $DB;

    /**
    * Name of the DB table
    *
    * @var  array
    */
    var $table_name = '';

    /**
    * namen der benoetigten db-spalten
    *
    * @var  array
    */
    var $table_fields = array();


    /**
     * Constructor
     *
     * @return void
     */
    function NestedSet () {
        $this->DB = &DB::singleton();
        
        $this->MyLink = $this->DB->link();
        
        $this->table_fields = array(
            'id'      => 'id',
            'lft'     => 'lft',
            'rgt'     => 'rgt',
            'root_id' => 'root_id',
            'parent_id' => 'parent_id'
        );

    }

    /**
     * Sets the name of the table
     *
     * @param string  table name
     * @return void
     */
    function set_table_name ($name) {
        $this->table_name = $name;
    }

    /**
     * namen der benoetigten db-spalten setzen
     *
     * @param array   associative array of strings (standard=>yours)
     * @return void
     */
    function set_field_names($names) {
        foreach($names AS $key => $val) {
            $this->table_fields[$key] = $val;
        }
    }

    /**
     * read nodes from db
     *
     * @access public
     * @param  mixed    id of root node (int), array of root_ids, NULL for all roots
     * @return mixed    array of found nodes or PEAR error object
     */
    function getNodes ($root=null, $fields = '*', $where = '')
    {

        if ($where != '') {
            $where = str_replace($this->table_name.'.', 'n.', $where);
        }
            
        //$sql= "    SELECT n.*,
        $sql= "    SELECT n.".implode(',n.',explode(',', $fields)).",
                    round((n.rgt-n.lft-1)/2,0) AS childs,
                    count(*)+(n.lft>1) AS level,
                    ((min(cast(p.rgt as signed))-cast(n.rgt as signed)-(cast(n.lft as signed)>1))/2) > 0 AS lower,
                    (( (n.lft-max(p.lft)>1) )) AS upper
                FROM ".$this->table_name." n, ".$this->table_name." p ";

        if (is_array($root)) {

            // multiple roots
            $sql.= "WHERE n.lft BETWEEN p.lft AND p.rgt
                        AND (p.root_id = n.root_id)
                        AND (p.id != n.id OR n.lft = 1)
                        AND (p.root_id IN (".join(",",$root)."))
                    GROUP BY n.root_id,n.id
                    ORDER BY n.root_id,n.lft";

        } elseif ((int)$root>0) {

            // single root
            $root=(int)$root;
            $sql.= "WHERE n.lft BETWEEN p.lft AND p.rgt
                        AND (p.root_id = n.root_id)
                        AND (p.id != n.id OR n.lft = 1)
                        AND (p.root_id = ".$root.")
                        $where
                    GROUP BY n.id
                    ORDER BY n.lft";

        } else {

            // all roots
            $sql.= "WHERE n.lft BETWEEN p.lft AND p.rgt
                        AND (p.root_id = n.root_id)
                        AND (p.id != n.id OR n.lft = 1)
                        $where
                    GROUP BY n.root_id,n.id
                    ORDER BY n.root_id,n.lft";

        }

        $res = $this->DB->query($sql);

        if (is_error($res)) 
        {
            $res->add("DB select failed: $sql");
            return $res;
        }
        
        $tree = array();
        
        while ($res->next(true)) 
        {
            $tree[] = $res->r();
        }
        
        return $tree;
    }


    /**
     * read single node from db
     *
     * @access public
     * @param  int      id of node
     * @return mixed    array of found node or PEAR error object
     */
    function getNode ($id=NULL)
    {

        if (isset($id)) {

            $sql = "SELECT
                        n.*,
                        round((n.rgt - n.lft-1)/2,0) AS childs
                    FROM
                        ".$this->table_name." AS n
                    WHERE
                        n.id = ".$id."
                    ";
            $res = $this->DB->query($sql,false);
            if (is_error($res)) {
                return $res;
            }
            
            if ($res->nr() > 0) {
                return $res->r();
            }
            else {
                return new Error("Node not found");
            }

        } else {
            return new Error('Wrong param');
        }
    }


    /**
     * gets path from a node
     *
     * @access public
     * @param  int      id of node
     * @return mixed    array of found node or PEAR error object
     */
    function getPath ($id=NULL,$fields = array('*'))
    {

        if ((int)$id > 0) {

            $id = (int)$id ;

            $sql = "SELECT
                        p.".implode(',p.', $fields)."
                    FROM
                        ".$this->table_name." AS n, ".$this->table_name." AS p
                    WHERE

                        n.id = ".$id."
                        AND
                        n.lft >= p.lft
                        AND
                        n.rgt <= p.rgt
                        AND
                        p.root_id = n.root_id
                    ORDER BY p.lft
                    ";
            $res = $this->DB->query($sql);
            if (is_error($res)) {
                return $res;
            }
            while ($res->next()) {
                $path[] = $res->r();
            }
            return $path;

        } else {
            return new Error('Wrong param');
        }
    }
    
    
    /**
     * gets complete subtree starting at a node
     *
     * @access public
     * @param  int      id of node
     * @return mixed    array of found nodes or error object
     */
    function getSubtree($id, $fields = '*', $where = '', $root = 1) 
    {
    
        if ($where != '') 
        {
            $where = str_replace($this->table_name.'.', 'n.', $where);
        }
    
        $id = (int)$id;
        if (!$id) return new Error('Wrong param: getSubtree');

        // to compute the level, whe have to know 'lft' and 'rgt' for the starting node
        $node = $this->getNode($id);
        if (is_error($node)) return $node;
        
        $lft = (int)$node['lft'];
        $rgt = (int)$node['rgt'];
        
        $sql= "SELECT n.".implode(',n.',explode(',', $fields)).",".
                    " round((n.rgt-n.lft-1)/2,0) AS childs, ".
                    " count(*)+(n.lft>1) AS level ".
                " FROM ".$this->table_name." n, ".$this->table_name." p ".
                " WHERE n.lft BETWEEN p.lft AND p.rgt ".
                    " AND (p.root_id = n.root_id) AND p.root_id=".$root." ".
                    " AND n.lft>=$lft AND n.rgt<=$rgt".
                    " AND (p.id != n.id OR n.lft = 1) ".
                    $where
                ." GROUP BY n.id".
                " ORDER BY n.lft";
            $res = $this->DB->query($sql);
            if (is_error($res)) {
                return $res;
            }
            while ($res->next()) {
                $path[] = $res->r();
            }
            return $path;
    }

    function getLevel($id, $fields = '*', $where = '', $root = 1) 
    {
    
        if ($where != '') 
        {
            $where = str_replace($this->table_name.'.', 'n.', $where);
        }
    
        $id = (int)$id;
        if (!$id) return new Error('Wrong param: getSubtree');

        // to compute the level, whe have to know 'lft' and 'rgt' for the starting node
        $node = $this->getNode($id);
        if (is_error($node)) return $node;
        
        $lft = (int)$node['lft'];
        $rgt = (int)$node['rgt'];
        
        $sql= "SELECT n.".implode(',n.',explode(',', $fields)).",".
                    " round((n.rgt-n.lft-1)/2,0) AS childs, ".
                    " count(*)+(n.lft>1) AS level ".
                " FROM ".$this->table_name." n, ".$this->table_name." p ".
                " WHERE n.lft BETWEEN p.lft AND p.rgt ".
                    " AND (p.root_id = n.root_id) AND p.root_id=".$root." ".
                    " AND n.lft>=$lft AND n.rgt<=$rgt".
                    " AND (p.id != n.id OR n.lft = 1) ".
                    $where
                ." GROUP BY n.id".
                " ORDER BY n.lft";
            $res = $this->DB->query($sql);
            if (is_error($res)) 
            {
                return $res;
            }
            while ($res->next()) 
            {
                /////////////very unclean!!!! find a better way soon
                if(!$level)
                {
                    $level = $res->f('level');
                    $level++;
                }
                if($res->f('level') != $level)
                {
                    continue;
                }
                $path[] = $res->r();
            }
            return $path;
    }

    /**
     * updates node record with additional values
     *
     * @access public
     * @param  int      node id
     * @param  array    associative (fieldname=>fieldvalue)
     * @return mixed    true or PEAR error object
     */
    function setNode ($id,$values) {
    
    

        if ( isset($id) && is_array($values) ) {
            
            foreach ($values AS $name=>$value) {
                $items[]= $name." = '".$value."'";
                
                
            }
            
            $sql = "UPDATE
                        ".$this->table_name."
                    SET
                        ".join(",",$items)."
                    WHERE
                        id = ".$id."
                    ";

            $res = $this->DB->query($sql);
            
            if (is_error($res)) {
                return res;
            } else {
                return true;
            }

        } else {
            return new Error('Wrong params');
        }

    }


    /**
     * insert new node
     *
     * TODO: error handling for each query
     *
     * @access public
     * @param  int      id of parent node or 0 for new root node
     * @return mixed    id of new node or PEAR error object
     */
    function nodeNew ($parent_id=0)
    {
        $parent_id=(int)$parent_id;

        if ( $parent_id > 0 ) 
        {
            if ($parent = $this->getNode($parent_id)) 
            {
                // update existing nodes
                $sql = "UPDATE ".$this->table_name."
                        SET lft = lft + 2
                        WHERE     root_id=".$parent["root_id"]."
                                AND
                                lft>".$parent["rgt"]."
                                AND rgt>=".$parent["rgt"]."
                        ";
                $res = $this->DB->query($sql,false);
                $sql = "UPDATE ".$this->table_name."
                        SET rgt=rgt+2
                        WHERE
                            root_id=".$parent["root_id"]."
                            AND
                            rgt>=".$parent["rgt"]."
                        ";
                $res = $this->DB->query($sql,false);

                // insert new node
                $sql = "INSERT INTO ".$this->table_name."
                            (root_id,lft,rgt)
                        VALUES
                            (".$parent["root_id"].", ".$parent["rgt"].", ".($parent["rgt"]+1).")
                        ";
                $res = $this->DB->query($sql,false);

                // get id of new node
                $sql = "SELECT * FROM ".$this->table_name."
                        WHERE
                            root_id=".$parent["root_id"]."
                            AND
                            lft=".$parent["rgt"]."
                        ";
                
                $res = $this->DB->query($sql,false);

                if (is_error($res)) 
                {
                    return $res;
                } 
                else 
                {   
                    if ($res->nr() > 0) 
                    {
                        return $res->f('id');
                    } 
                    else 
                    {
                        return new Error('Unknown error');
                    }
                }
            } 
            else 
            {
                return new Error('Parent node not found');
            }
        } 
        else 
        {

            $sql = "INSERT INTO ".$this->table_name."
                        (root_id,lft,rgt)

                    VALUES
                        (0,1, 2)
                    ";
            $res = $this->DB->query($sql,false);

            $sql = "SELECT     id
                    FROM     ".$this->table_name."
                    WHERE     root_id=0";
            $res = $this->DB->query($sql,false);

            $root_id = $res->f("id");

            $sql = "UPDATE    ".$this->table_name."
                    SET        root_id=".$root_id."
                    WHERE    id=".$root_id;
            $res = $this->DB->query($sql,false);

            return $root_id;

        }
    }


    /**
     * delete node
     *
     * TODO: error handling for each query
     *
     * @access public
     * @param  int      node id
     * @param  boolean  true for deleting nodes with childs
     * @return mixed    true or PEAR error object
     */
    function nodeDel ($id=0,$deleteChilds=NULL)
    {
        $id = (int) $id;

        if ( $id > 0 ) {

            $node = $this->getNode($id);

            if (!is_error($node)) {

                if ( ($node["rgt"]==$node["lft"]+1)) {

                    $sql = "UPDATE     ".$this->table_name."
                            SET     lft=lft-2
                            WHERE    lft > ".$node["lft"]."
                                    AND root_id = ".$node["root_id"];

                    $res = $this->DB->query($sql);

                    $sql = "UPDATE     ".$this->table_name."
                            SET     rgt=rgt-2
                            WHERE     rgt > ".$node["rgt"]."
                                AND root_id = ".$node["root_id"];

                    $res = $this->DB->query($sql);

                    $sql = "DELETE
                            FROM     ".$this->table_name."
                            WHERE     id = ".$id;

                    $res = $this->DB->query($sql);
                    

                    return true;
                    
                } elseif ($deleteChilds === '1') {

                    $sql = "DELETE
                            FROM    ".$this->table_name."
                            WHERE    rgt <= ".$node["rgt"]."
                                AND    lft >= ".$node["lft"]."
                                AND    root_id = ".$node["root_id"];
                                

                                
                    $res = $this->DB->query($sql);

                    $sql = "UPDATE    ".$this->table_name."
                            SET        rgt = rgt - ".($node["rgt"]-$node["lft"]+1)."
                            WHERE     rgt > ".$node["rgt"]."
                                AND    root_id = ".$node["root_id"];

                    $res = $this->DB->query($sql);

                    $sql = "UPDATE    ".$this->table_name."
                            SET        lft = lft - ".($node["rgt"]-$node["lft"]+1)."
                            WHERE     lft > ".$node["rgt"]."
                                AND    root_id = ".$node["root_id"];

                    $res = $this->DB->query($sql);

                    return true;

                } else {
                    return new Error('Node not empty ('.$id.')');
                }

            } else {
                return new Error('Node not found ('.$id.')');
            }

        } else {
            return new Error('Wrong node id ('.$id.')');
        }

    }

    /**
    * moves node
    */
    function MoveNode ($IDNode = -1, $IDParent = -1, $Order = -1, $Differ = "1")
    {
        $this->FieldID = "id"; 
        $this->FieldIDParent = "parent_id"; 
        $this->FieldLeft = "lft"; 
        $this->FieldRight = "rgt"; 
        $this->FieldDiffer = "root_id";
        $this->FieldLevel = "NSLevel"; 
        $this->FieldOrder = "lft"; 
        $this->FieldIgnore = "set_ignore";
        
        $sql_select = "SELECT * FROM " . $this->table_name .
                      " WHERE " . $this->FieldID . " = " . $IDNode .
                      " AND " . $this->FieldDiffer . " = '" . $Differ . "'";

        $rs_select = $this->_safe_query ($sql_select, $this->MyLink);
        if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
        {
            $this->_safe_set ($row_select[$this->FieldID], -1);
            $this->_safe_set ($row_select[$this->FieldLeft], -1);
            $this->_safe_set ($row_select[$this->FieldRight], -1);
            $this->_safe_set ($row_select[$this->FieldLevel], -1);
            $delete_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft];


            $sql_select_parent = "SELECT * FROM " . $this->table_name .
                                 " WHERE " . $this->FieldID . " = " . $IDParent .
                                 " AND " . $this->FieldDiffer . " = '" . $Differ . "'";

            $rs_select_parent = $this->_safe_query ($sql_select_parent, $this->MyLink);
            if (($rs_select_parent) && ($row_select_parent = mysql_fetch_assoc ($rs_select_parent)))
            {
                $this->_safe_set ($row_select_parent[$this->FieldID], -1);
                $this->_safe_set ($row_select_parent[$this->FieldLeft], -1);
                $this->_safe_set ($row_select_parent[$this->FieldRight], -1);
                $this->_safe_set ($row_select_parent[$this->FieldLevel], -1);

                $left = $row_select_parent[$this->FieldLeft] + 1;

                
                //Set node tree as ignore
                $sql_ignore = "UPDATE " . $this->table_name .
                              " SET " . $this->FieldIgnore . " = 1" .
                              " WHERE " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
                              " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] .
                              " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query ($sql_ignore, $this->MyLink);
                /**/
                // Update Order (set order = order +1 where order>$Order)
                if ($Order == -1)
                {
                    $sql_order = "SELECT * FROM " . $this->table_name .
                                 " WHERE " . $this->FieldIDParent . " = " . $IDParent .
                                 " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
                                 " ORDER BY " . $this->FieldOrder . " DESC " .
                                 " LIMIT 0,1";
                    $rs_order = $this->_safe_query ($sql_order, $this->MyLink);
                    if (($rs_order) && ($row_order = mysql_fetch_assoc ($rs_order)))
                    {
                        $this->_safe_set ($row_order[$this->FieldOrder], 0);
                        $Order = $row_order[$this->FieldOrder] + 1;
                        mysql_free_result ($rs_order);
                    }
                    else
                    { $Order = 1; }
                }

                $sql_update = "UPDATE " . $this->table_name .
                              " SET " . $this->FieldOrder . " = " . $this->FieldOrder . " + 1" .
                              " WHERE " . $this->FieldIDParent . " = " . $IDParent .
                              " AND " . $this->FieldOrder . " >= " . $Order .
                              " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query ($sql_update, $this->MyLink);
                /**/
                
                $sql_order = "SELECT * FROM " . $this->table_name .
                             " WHERE " . $this->FieldIDParent . " = " . $IDParent .
                             " AND " . $this->FieldOrder  . " <= " . $Order .
                             " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
                             " ORDER BY " . $this->FieldOrder . " DESC " .
                             " LIMIT 0,1";
                $rs_order = $this->_safe_query ($sql_order, $this->MyLink);
                if (($rs_order) && ($row_order = mysql_fetch_assoc ($rs_order)))
                {
                    $this->_safe_set ($row_order[$this->FieldRight], -1);
                    $left = $row_order[$this->FieldRight] + 1;
                    mysql_free_result ($rs_order);
                }

                $child_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft] + 1;

                // Update FieldLeft
                if ($left < $row_select[$this->FieldLeft]) // Move to left
                {
                    $sql_update = "UPDATE " . $this->table_name .
                                  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " + (" . $child_offset . ")" .
                                  " WHERE " . $this->FieldLeft . " >= " . $left .
                                  " AND " . $this->FieldLeft . " <= " . $row_select[$this->FieldLeft] .
                                  " AND " . $this->FieldIgnore . " = 0" .
                                  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                }
                else // Move to right
                {
                    $sql_update = "UPDATE " . $this->table_name .
                                  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " - " . $child_offset .
                                  " WHERE " . $this->FieldLeft . " <= " . $left .
                                  " AND " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
                                  " AND " . $this->FieldIgnore . " = 0" .
                                  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                }
                $this->_safe_query ($sql_update, $this->MyLink);

                // Update FieldRight
                if ($left < $row_select[$this->FieldLeft]) // Move to left
                {
                    $sql_update = "UPDATE " . $this->table_name .
                                  " SET " . $this->FieldRight . " = " . $this->FieldRight . " + (" . $child_offset . ")" .
                                  " WHERE " . $this->FieldRight . " >= " . $left .
                                  " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] .
                                  " AND " . $this->FieldIgnore . " = 0" .
                                  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                }
                else // Move to right
                {
                    $sql_update = "UPDATE " . $this->table_name .
                                  " SET " . $this->FieldRight . " = " . $this->FieldRight . " - " . $child_offset .
                                  " WHERE " . $this->FieldRight . " < " . $left .
                                  " AND " . $this->FieldRight . " >= " . $row_select[$this->FieldRight] .
                                  " AND " . $this->FieldIgnore . " = 0" .
                                  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                }
                $this->_safe_query ($sql_update, $this->MyLink);

                $level_difference = $row_select_parent[$this->FieldLevel] - $row_select[$this->FieldLevel] + 1;
                $new_offset = $row_select[$this->FieldLeft] - $left;
                if ($left > $row_select[$this->FieldLeft]) // i.e. move to right
                { $new_offset += $child_offset; }

                //Update new tree left
                $sql_update = "UPDATE " . $this->table_name .
                              " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " - (" . $new_offset . "), " .
                              $this->FieldRight . " = " . $this->FieldRight . " - (" . $new_offset . ") " .
                              //"$this->FieldLevel = $this->FieldLevel + $level_difference" .
                              " WHERE " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
                              " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] .
                              " AND " . $this->FieldIgnore . " = 1" .
                              " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query ($sql_update, $this->MyLink);

                $sql_update = "UPDATE " . $this->table_name .
                              " SET " . $this->FieldOrder . " = " . $this->FieldOrder . " - 1" .
                              " WHERE " . $this->FieldIDParent . " = " . $IDParent .
                              " AND " . $this->FieldOrder . " > " . $Order .
                              " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query ($sql_update, $this->MyLink);

                
                //Remove ignore statis from node tree
                $sql_ignore = "UPDATE " . $this->table_name .
                              " SET " . $this->FieldIgnore . " = 0" .
                              " WHERE " . $this->FieldLeft . " >= " . ($row_select[$this->FieldLeft] - $new_offset) .
                              " AND " . $this->FieldRight . " <= " . ($row_select[$this->FieldRight] - $new_offset) .
                              " AND " . $this->FieldIgnore . " = 1" .
                              " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
                $this->_safe_query ($sql_ignore, $this->MyLink);
                
                //Update insert root field
                /*
                $sql_update = "UPDATE " . $this->table_name . " SET " . $this->FieldIDParent . " = " . $IDParent . ", " .
                              $this->FieldOrder . " = " . ($Order) . " WHERE " . $this->FieldID . " = " . $IDNode;
                */
                $sql_update = "UPDATE " . $this->table_name . " SET " . $this->FieldIDParent . " = " . $IDParent . " " .
                             " WHERE " . $this->FieldID . " = " . $IDNode;
                $this->_safe_query ($sql_update, $this->MyLink);

                mysql_free_result ($rs_select_parent);
                //UTIL::debug($this);
                return true;
            }
            else
            { return false; }

            mysql_free_result ($rs_select);
            return true;
        }
        else
        { return false; }
    }

    
    function _safe_query ($query, $link)
    {
        if (empty($query)) { return false; }
        //MC::debug($query, 'QUERY');
        $result = mysql_query ($query, $link) or die("Error executing query: $query");
        return $result;
    }
    function _safe_set (&$var_true, $var_false = "")
    {
        if (!isset ($var_true)) 
        { $var_true = $var_false; }
    }

    
    
    
    /**
     * move a node
     *
     * TODO: more options to move subtrees
     *
     * @access public
     * @param  int      node id
     * @param  string   "up" for move upward
     * @return mixed    true or PEAR error object
     */
     
    function nodeMove($id,$d='up')
    {
        if($d == 'up')
        {
            $sql = "SELECT n1.root_id,
                n1.lft n1lft, n1.rgt n1rgt,
                n2.lft n2lft, n2.rgt n2rgt
                FROM    ".$this->table_name." AS n1
                LEFT OUTER JOIN
                ".$this->table_name." AS n2
                ON 
                (
                    n1.lft = (n2.rgt+1)
                    AND
                    n1.rgt > n2.rgt
                    AND
                    n1.root_id = n2.root_id
                )
                WHERE    n1.id=$id";
                
            $res = $this->DB->query($sql);
            $nodes = $res->r();
            $root_id = $nodes["root_id"];
            
            $desc = (($nodes["n1lft"]-$nodes["n2lft"]));
            $inc = (($nodes["n1rgt"]-$nodes["n2rgt"]));
            
            $sql = "UPDATE ".$this->table_name."
                SET
                lft=lft + IF(lft<".$nodes["n1lft"].",$inc,-$desc),
                rgt=rgt + IF(rgt<".$nodes["n1lft"].",$inc,-$desc)
                WHERE    root_id=$root_id
                AND
                lft>=".$nodes["n2lft"]."
                AND
                rgt<=".$nodes["n1rgt"]."";
                
        }
        else
        {
            $sql = "SELECT n1.root_id,
                n1.lft n1lft, n1.rgt n1rgt,
                n2.lft n2lft, n2.rgt n2rgt
                FROM    ".$this->table_name." AS n1
                LEFT OUTER JOIN
                ".$this->table_name." AS n2
                ON 
                (
                    (n1.rgt+1) = n2.lft
                    AND
                    n1.rgt < n2.rgt
                    AND
                    n1.root_id = n2.root_id
                )
                WHERE    n1.id=$id";
                
            $res = $this->DB->query($sql);
            $nodes = $res->r();
            $root_id = $nodes["root_id"];
            
            $desc = (($nodes["n2lft"]-$nodes["n1lft"]));
            $inc = (($nodes["n2rgt"]-$nodes["n1rgt"]));
            
            $sql = "UPDATE ".$this->table_name."
                SET
                lft=lft + IF(lft<".$nodes["n2lft"].",$inc,-$desc),
                rgt=rgt + IF(rgt<".$nodes["n2lft"].",$inc,-$desc)
                WHERE    root_id=$root_id
                AND
                lft>=".$nodes["n1lft"]."
                AND
                rgt<=".$nodes["n2rgt"]."";
                
        }


        $this->DB->query($sql);
        
     }
}
?>
