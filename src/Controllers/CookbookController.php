<?php

namespace Controllers;

use Illuminate\Database\QueryException;
use Models\Cookbook;
use Psr\Container\ContainerInterface;
use Services\Auth;
use Slim\Http\Request;
use Slim\Http\Response;

class CookbookController
{
    /** @var Auth*/
    protected $auth;

    /** @var string*/
    protected $upload_directory;

    /**
     * Constructor receives container instance
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->upload_directory = $container->get('upload_directory');
    }

    /**
     * Return list of Cookbooks
     *
     * Request Parameters token
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson(['unauthorized'], 401);
        }

        $cookbooks = Cookbook::all();

        return $response->withJson(['all cookbooks' => $cookbooks]);
    }

    /**
     * View item Cookbook
     *
     * Request Parameters token
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return Response
     */
    public function view(Request $request, Response $response, array $args): Response
    {
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson(['unauthorized'], 401);
        }

        $cookbook = Cookbook::query()->where('id', $args['id'])->first();

        return $response->withJson(['view cookbook' => $cookbook]);
    }

    /**
     * Create a new Cookbook
     *
     * Request Parameters token, cookbook[title], cookbook[author], cookbook[description], cookbook[text]
     * Request Body File upload (multipart/form-data)
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $requestUserId = ($requestUser = $this->auth->requestUser($request))->id;

        if (is_null($requestUser)) {
            return $response->withJson(['unauthorized'], 401);
        }

        try {
            $cookbook = new Cookbook($request->getParam('cookbook'));
            $cookbook->user_id = $requestUserId;
            if ($cookbook->save()) {
                if ($cookbook->image = ($this->uploadFile($request, $cookbook->id)))
                    $cookbook->save();
            }
        } catch (QueryException $exception)
        {
            return $response->withJson(['QueryException' => $exception->getCode()]);
        }

        return $response->withJson(['cookbook' => $cookbook]);
    }

    /**
     * Update Cookbook (set params for update)
     *
     * Request Parameters token
     * Request Parameters For Update cookbook[title], cookbook[description], cookbook[body]
     * Request Body File upload (multipart/form-data)
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $cookbook = Cookbook::query()->where('id', $args['id'])->first();

        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson(['unauthorized'], 401);
        }

        if($requestUser->id != $cookbook->user_id){
            return $response->withJson(['message' => 'Forbidden'], 403);
        }

        $params = $request->getParam('cookbook', []);

        try {
            $cookbook->update([
                'title' => isset($params['title']) && $params['title'] ? $params['title'] : $cookbook->title,
                'description' => isset($params['description']) ? $params['description'] : $cookbook->description,
                'body' => isset($params['body']) ? $params['body'] : $cookbook->body,
            ]);

            if ($files = $request->getUploadedFiles()) {
                $cookbook->update(['image' => $this->uploadFile($request, $cookbook->id, $cookbook->image)]);
            }
        } catch (QueryException $exception){
            return $response->withJson(['QueryException' => $exception->getCode()]);
        }

        return $response->withJson(['cookbook' => $cookbook]);
     }

    /**
     * Delete Cookbook
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response  $response
     * @param array $args
     *
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {

        $responseUserId = ($responseUser = $this->auth->requestUser($request))->id;

        $cookbook = Cookbook::query()->where(['id' => $args['id'], 'user_id' => $responseUserId])->first();

        if (is_null($responseUser)){
            return $response->withJson(['unauthorized'], 401);
        }

        if ($responseUserId != $cookbook->user_id){
            return $response->withJson(['message' => 'Forbidden'], 403);
        }

        if ($cookbook->delete()){
            $this->deleteFile($cookbook->id,$cookbook->image);
        }

        return $response->withJson(['message' => 'Cookbook deleted']);
    }

    /**
     * Upload file into server
     *
     * @param \Slim\Http\Request  $request
     * @param integer $cookbook_id
     * @param string $image
     *
     * @return bool|string
     */
    public function uploadFile(Request $request, $cookbook_id, $image = null):? string
    {
        $file_directory = $this->upload_directory . '/' . $cookbook_id;

        if ( ! $files = $request->getUploadedFiles())
            return false;

        $file = array_shift($files);
        $ext = array_pop(explode('.', $file->getClientFilename()));
        $file_name = time() . '.' . $ext;

        if ( ! $file->getError()) {
            mkdir($file_directory);
            ($image) ? unlink($file_directory . '/' . $image) : false;
            $file->moveTo($file_directory . '/' . $file_name);

            return $file_name;
        }

        return false;
    }

    /**
     * Delete file into server
     *
     * @param integer $cookbook_id
     * @param string $image
     *
     * @return void
     */
    public function deleteFile($cookbook_id, $image): void
    {
        $file_directory = $this->upload_directory . '/' . $cookbook_id;

        unlink($file_directory . '/' . $image);
        rmdir($file_directory);
    }
}