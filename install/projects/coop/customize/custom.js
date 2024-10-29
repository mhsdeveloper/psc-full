		/*
			customize the format of names here, with these parts, and any punctuation inbetween that you need:
			LAST_NAME
			FIRST_NAME
			BIRTH_NAME

		*/
		const NAME_FORMAT = "TITLE FIRST_NAME MIDDLE_NAME BIRTH_NAME LAST_NAME";


		const FULL_NAME_FORMAT = "TITLE FIRST_NAME MIDDLE_NAME BIRTH_NAME LAST_NAME"; ;//"TITLE FIRST_NAME MIDDLE_NAME LAST_NAME SUFFIX[(born FIRST_NAME BIRTH_NAME)]";


		const TITLE_STOP_WORDS = ["Mr.", "Mr", "Mrs.", "Mrs", "Ms", ";"];


		/* customize the date format here, with these parts, and any punctuation inbetween:
			DAY
			MONTH
			YEAR

			MONTH3 is just the first three letters of the month
			MONTH3ALT is the first three letters, except for four letter months, that are just the full month

		*/

		const DATE_FORMAT = "DAY MONTH3 YEAR";

			