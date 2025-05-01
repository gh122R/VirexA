<?php

declare(strict_types = 1);
namespace App;

use App\Helpers\ErrorHandler;

class Router
{
    private array $routes;
    private array $dinamicRoutes;

    public function __construct()
    {
        $this->routes = [];
        $this->dinamicRoutes = [];
    }

    /**
     * Методы для регистрации маршрутов. Они принимают строку с маршрутом, действие, которое будет выполнять вызов функции или контроллера и
     * middleware, который может быть, как функцией, так и массивом.
    */
    public function get(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->registerRoute($route, $action, 'GET', $middleware);
        return $this;
    }

    public function post(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->registerRoute($route, $action, 'POST', $middleware);
        return $this;
    }

    public function put(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->registerRoute($route, $action, 'PUT', $middleware);
         return $this;
    }

    public function delete(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->registerRoute($route, $action, 'DELETE', $middleware);
        return $this;
    }

    private function registerRoute(string $route, array|callable $action ,string $method, array|callable $middleware = []): void
    {
        if (str_contains($route, '{'))
        {
            $pattern = "#^" . preg_replace('#\{(\w+)\}#', '(\w+)', $route) . "$#";
            preg_match_all('/\{(\w+)\}/', $route, $matches);
            $this->dinamicRoutes[$route] = [
                'action' => $action,
                'method' => $method,
                'middlewareList' => $middleware,
                'matches' => $matches[1],
                'pattern'=> $pattern
            ];
        }else
        {
            $this->routes[$route] = [
                'action' => $action,
                'method' => $method,
                'middlewareList' => $middleware
            ];
        }
    }

    private function checkParameters(callable|array $parameters, callable $next): mixed
    {
        if (is_callable($parameters))
        {
            $parameters = [$parameters];
        }
        foreach (array_reverse($parameters) as $parameter)
        {
            if (is_array($parameter))
            {
                if (count($parameter) === 3)
                {
                    [$class, $method, $argument] = $parameter;
                    $classInstance = $this->createClassInstance($class, $argument) ?? ErrorHandler::brokenClassInstance($class);
                }elseif(count($parameter) === 2)
                {
                    [$class, $method] = $parameter;
                    $classInstance = $this->createClassInstance($class) ?? ErrorHandler::brokenClassInstance($class);
                }
                if (isset($classInstance, $class, $method) && is_object($classInstance))
                {
                    if (method_exists($class, $method))
                    {
                        $next = function () use ($classInstance, $next, $method)
                        {
                            return $classInstance->$method($next);
                        };
                    }else
                    {
                        return ErrorHandler::methodNotFound($method, $class);
                    }
                }else
                {
                    return $classInstance ?? null;
                }
            }elseif(is_callable($parameter))
            {
                $next = function () use ($parameter, $next)
                {
                    return $parameter($next);
                };
            }
        }
        return $next();
    }

    private function createClassInstance(string $class, mixed $argument = null): object|string
    {
        if (class_exists($class))
        {
            return $argument !== null ? new $class($argument) : new $class();
        }else
        {
            return ErrorHandler::classNotFound($class);
        }
    }

    /**
     *  handler - это обработчик маршрутов. Он берёт полученный адрес и ищет совпадения в массиве $routes, далее
     * проверяет заданный метод(GET,POST,PUT,DELETE) в зарегистрированном маршруте с отправленным методом(GET,POST,PUT,DELETE).
     * Если всё окей, то создаём экземпляр переданного контроллера и обращаемся к его методу, указанному при регистрации маршрута.
     * Если вместо контроллера передана функция, то вызываем её.
     * Всё это происходит при вызове замыкания $next(), но мы не вызываем её здесь сразу на прямую, почему?
     * Это сделано для проверки заданных middleware'ов, вместо $next() мы возвращаем вызов метода checkParameters,
     * который проходит по цепочке middleware' ов и в конечном итоге вызывает $next().
    */

    public function handler(string $uri): mixed
    {
        $route = parse_url($uri, PHP_URL_PATH);
        $routeData = $this->routes[$route] ?? null;
        $parameters = null;
        if(!$routeData)
        {
            foreach ($this->dinamicRoutes as $matchRoute => $data)
            {
                if(preg_match($data['pattern'], $route, $matches))
                {
                    array_shift($matches);
                    $parameters = array_combine($data['matches'], $matches);
                    $routeData = $data;
                    break;
                }
            }
            if(!$routeData)
            {
                return ErrorHandler::routeNotFound($route);
            }
        }
        $next = function () use ($routeData, $route, $parameters)
        {
            if ($routeData !== null && $_SERVER['REQUEST_METHOD'] === $routeData["method"])
          {
              if (is_callable($routeData["action"]))
              {
                   $response =  call_user_func($routeData["action"]);
              }elseif (is_array($routeData["action"]))
              {
                  [$class, $method] = $routeData["action"];
                  $parameters !== null ? $controller = $this->createClassInstance($class, $parameters) : $controller = $this->createClassInstance($class);
                  if (!is_object($controller))
                  {
                      return ErrorHandler::controllerNotFound($class);
                  }else
                  {
                      method_exists($controller, $method) ? $response = call_user_func([$controller, $method]) : $response= ErrorHandler::methodNotFound($method, $class);
                  }
              }
          }
            elseif($_SERVER['REQUEST_METHOD'] !== $routeData["method"])
            {
                return ErrorHandler::failedRequestMethod($route, $routeData["method"], $_SERVER['REQUEST_METHOD']);
            }
            return $response ?? ErrorHandler::failedHandlerResponse();
        };

        $middlewareList = $routeData["middlewareList"] ?? null;
        return $this->checkParameters($middlewareList, $next);
    }
}