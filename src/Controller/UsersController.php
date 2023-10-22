<?php

declare(strict_types=1);

namespace Latent\ElAdmin\Controller;

use Latent\ElAdmin\Exceptions\ValidateException;
use Latent\ElAdmin\Models\GetModelTraits;
use Illuminate\Http\JsonResponse;
use Latent\ElAdmin\Services\UserService;
use Throwable;

class UsersController extends Controller
{
    use GetModelTraits;

    /**
     * @param UserService $userService
     * @return JsonResponse
     * @throws ValidateException
     */
    public function index(UserService $userService): JsonResponse
    {
        $params = $this->validator([
            'name'  => 'string|min:1,max:20',
            'email' => 'string|min:1,max:30',
            'page'  => 'integer',
            'page_size' => 'integer',
        ]);

        return $this->success($userService->list($params));
    }


    /**
     * @param UserService $userService
     * @return JsonResponse
     */
    public function store(UserService $userService): JsonResponse
    {
        try {
            $params = $this->validator([
                'name' => 'required|string|'.$this->getTableRules('unique','users_table'),
                'email' => 'required|email|'.$this->getTableRules('unique','users_table'),
                'password' => 'required|min:6|max:20|confirmed:password_confirmation',
                'password_confirmation' => 'required|min:6|max:20',
                'rule' => 'array',
            ]);

            $userService->add($params);
        }catch (Throwable $e) {
            return  $this->fail($e->getMessage().$e->getFile().$e->getLine());
        }

        return $this->success();
    }


    /**
     * @param $id
     * @param UserService $userService
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidateException
     */
    public function update($id, UserService $userService): JsonResponse
    {
        $params = $this->validator([
            'id'       => 'required|'.$this->getTableRules('exists','users_table'),
            'name'     => 'string|'.$this->getTableRules('unique','users_table','id'),
            'email'    => 'email|'.$this->getTableRules('unique','users_table','id'),
            'password' => 'nullable|min:6|max:20|confirmed:password_confirmation',
            'password_confirmation' => 'nullable|min:6|max:20',
            'rule'     => 'array',
        ], $this->mergeParams(['id' => $id]));

        $userService->update($params);

        return $this->success();
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->getUserModel()->where('id', $id)->delete();

        return $this->success();
    }
}
