<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\Cms\ForbiddenException;
use App\Init\AuthInit;
use App\Model\Cms\LinUser;
use App\Service\TokenService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Contract\RequestInterface;


class BackendAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @Inject()
     * @var TokenService
     */
    private $token;

    /**
     * @Inject()
     * @var LinUser
     */
    private $user;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatch = $this->request->getAttribute(Dispatched::class);
        list($class,$method) = $dispatch->handler->callback;
        $routeName = AuthInit::makeKey($class, $method);
        $auth = AuthInit::get($routeName);
        if (empty($auth)) { // 没有设置权限代表所有用户都可以访问
            return $handler->handle($request);
        }
        $authName = $auth['authName'];
        $login = $auth['login'];
        $moduleName = $auth['moduleName'];

        if (!$login) { // 接口不需要登录
            return $handler->handle($request);
        }
        // 没登录会自动抛异常
        $uid = $this->token->getCurrentUID();
        if ($moduleName === "必备") {
            return $handler->handle($request);
        }

        $permission = $this->user->getUserAllPermission($uid);
        if (in_array($authName, $permission)) {
            return $handler->handle($request);
        }

        throw new ForbiddenException();
    }

}