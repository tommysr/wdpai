<?php

interface IRequest
{
  public function isGet();
  public function isPost();
  public function get(string $key);
  public function post(string $key);
  public function query(string $key);
}

enum RequestMethod
{
  case GET;
  case POST;
}

class Request
{
  private RequestMethod $requestMethod;
  private array $queries;

  public function __construct()
  {
    $this->requestMethod = $_SERVER['REQUEST_METHOD'] === 'GET' ? RequestMethod::GET : RequestMethod::POST;
    $this->queries = array();
    parse_str($_SERVER['QUERY_STRING'], $this->queries);
  }

  public function isGet()
  {
    return $this->requestMethod == RequestMethod::GET;
  }

  public function isPost()
  {
    return $this->requestMethod == RequestMethod::POST;
  }

  public function get(string $key)
  {
    return $_GET[$key] ?? null;
  }

  public function post(string $key)
  {
    return $_POST[$key] ?? null;
  }

  public function query(string $key)
  {
    return $this->queries[$key] ?? null;
  }

  public function queries()
  {
    return $this->queries;
  }
}