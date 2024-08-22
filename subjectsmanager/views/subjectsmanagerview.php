<?php
namespace SubjectsManager\Views;

class SubjectsManagerView { 
  public function respondWithErrors($code, $message = '') {
    $error_messages = [
      400 => 'Bad Request',
      401 => 'Unauthorized',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      415 => 'Unsupported Media Type',
      422 => 'Unprocessable Entity',
      429 => 'Too Many Requests',
      500 => 'Internal Server Error'
    ];
  
    if (empty($message) && isset($error_messages[$code])) {
      $message = $error_messages[$code];
    }
  
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
  }

  public function respondWithData($code, $data){
    http_response_code($code);
    echo json_encode($data);
    exit;
  }
  
  public function respond($code, $message = '') {
    $success_message = [
      200 => 'OK',
      201 => 'Created'
    ];
  
    if (empty($message) && isset($success_message[$code])) {
      $message = $success_message[$code];
    }
  
    http_response_code($code);
    echo json_encode(['success' => true]);
    exit;
  }
}