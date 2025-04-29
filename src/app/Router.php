<?php

declare(strict_types = 1);
namespace App;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * Методы для регистрации маршрутов. Они принимают строку с маршрутом, действие, которое будет выполнять вызов функции или контроллера и
     * middleware, который может быть, как функцией, так и массивом.
    */
    public function get(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->routes[$route] = [
            'action' => $action,
            'method' => 'GET',
            'middlewareList' => $middleware
        ];
        return $this;
    }

    public function post(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->routes[$route] = [
            'action' => $action,
            'method' => 'POST',
            'middlewareList' => $middleware
        ];
        return $this;
    }

    public function put(string $route, array|callable $action, array|callable $middleware = []): self
    {
         $this->routes[$route] = [
            'action' => $action,
            'method' => 'PUT',
            'middlewareList' => $middleware
        ];
         return $this;
    }

    public function delete(string $route, array|callable $action, array|callable $middleware = []): self
    {
        $this->routes[$route] = [
            'action' => $action,
            'method' => 'DELETE',
            'middlewareList' => $middleware
        ];
        return $this;
    }

    private function checkParameters(callable|array $parameters, callable $next): mixed
    {
        if (is_callable($parameters))
        {
            return call_user_func($parameters, $next);
        }
        foreach (array_reverse($parameters) as $parameter)
        {
            if (is_array($parameter))
            {
                if (count($parameter) === 3)
                {
                    [$class, $method, $argument] = $parameter;
                    $classInstance = $this->createClassInstance($class, $argument) ?? ErrorHandler::error('Ошибка при создании экземпляра контроллера');
                }elseif(count($parameter) === 2)
                {
                    [$class, $method] = $parameter;
                    $classInstance = $this->createClassInstance($class) ?? ErrorHandler::error('Ошибка при создании экземпляра контроллера');
                }
                if (isset($classInstance, $class, $method))
                {
                    if (method_exists($class, $method))
                    {
                        $next = function () use ($classInstance, $next, $method)
                        {
                            return $classInstance->$method($next);
                        };
                    }else
                    {
                        return ErrorHandler::error("Метод $method не найден в классе $class!");
                    }
                }
            }
        }
        return $next();
    }

    private function createClassInstance(string $class, mixed $argument = null): object|string
    {
        if (!class_exists($class))
        {
            return ErrorHandler::error("Класс $class не найден!");
        }
        return $argument !== null ? new $class($argument) : new $class();
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
        if(!$routeData)
        {
            return ErrorHandler::error("Путь $route не найден!");
        }
        $next = function () use ($routeData, $route)
        {
            if ($routeData !== null && $_SERVER['REQUEST_METHOD'] === $routeData["method"])
          {
              if (is_callable($routeData["action"]))
              {
                   $response =  call_user_func($routeData["action"]);
              }elseif (is_array($routeData["action"]))
              {
                  [$class, $method] = $routeData["action"];
                  if(class_exists($class))
                  {
                      $controller = new $class();
                  }else
                  {
                      return ErrorHandler::error("Контроллер $class не найден!");
                  }
                  method_exists($controller, $method) ? $response = call_user_func([$controller, $method]) : $response= ErrorHandler::error("Метод $method не найден!");
              }
          }
            elseif($_SERVER['REQUEST_METHOD'] !== $routeData["method"])
            {
                return ErrorHandler::error("Данный метод низя применить на маршруте: $route, поскольку запрос страницы {$_SERVER['REQUEST_METHOD']} не совпадает с методом маршрута $routeData[method]!");
            }
            return $response ?? ErrorHandler::error('Произошла ошибка при вызове замыкания 0_0. Виновник - handler роутера!');
        };

        $middlewareList = $routeData["middlewareList"] ?? null;
        return $this->checkParameters($middlewareList, $next);
    }
}