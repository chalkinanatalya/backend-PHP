<?php
namespace Project\Http\Actions\Post;

use Project\Exceptions\ArgumentException;
use Project\Exceptions\UserNotFoundException;
use Project\Http\Actions\ActionInterface;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Http\Response\Response;
use Project\Http\Response\SuccessfulResponse;
use Project\Blog\Post\Post;
use Project\Repositories\Post\PostRepositoryInterface;

class DeletePostAction implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $postId = $request->query('id');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postRepository->delete($postId);

        return new SuccessfulResponse([
            'status' => 'post with id: ' . $postId . 'has been deleted'
        ]);
    }
}