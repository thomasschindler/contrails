<?

final class error_message_level{
	const error = 0;
	const warning = 1;
	const debug = 2;

	static function s($level){
		switch ($level) {
			case error_message_level::error:
				return "error";
			case error_message_level::warning:
				return "warning";
			case error_message_level::debug:
				return "debug";
		}
	}
}