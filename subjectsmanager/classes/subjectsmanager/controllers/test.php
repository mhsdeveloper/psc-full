<?php

	namespace SubjectsManager\Controllers;

	class Test {

		public function __construct(){
		}

		public function index() {
			http_response_code(200);
			echo json_encode(['testing' => true]);
		}

		public function example() {

			$topics = new \SubjectsManager\Models\TopicsModel();

			$topics->test2();
		}


	}
