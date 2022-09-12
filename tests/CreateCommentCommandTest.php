<?php
namespace Tests\Commands\CommentCommand\CreateCommentCommandTest;

use Project\Comment\Comment;
use Project\Commands\CommentCommand\CreateCommentCommand;
use Project\Argument\Argument;
use Project\Exceptions\ArgumentException;
use PHPUnit\Framework\TestCase;
use Project\Repositories\Comment\CommentRepositoryInterface;
use Project\Exceptions\CommentNotFoundException;
class CreateCommentCommandTest extends TestCase
{
    private function makeCommentRepository(): CommentRepositoryInterface
    {
        return new class implements CommentRepositoryInterface
        {
            public function save(Comment $comment): void
            {
            }
            public function get(int $id): Comment
            {
                throw new CommentNotFoundException("Comment with id: $id not found");
            }
        };
    }
    public function testItRequiresPostId(): void
    {
        $command = new CreateCommentCommand($this->makeCommentRepository());
        $this->expectExceptionMessage('No such argument: postId');
        $command->handle(new Argument(['authorId' => '1', 'text' => 'testText1']));
    }
    public function testItRequiresAuthorId(): void
    {
        $command = new CreateCommentCommand($this->makeCommentRepository());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: authorId');
        $command->handle(new Argument(['postId' => '1', 'text' => 'testText2']));
    }
    public function testItRequiresText(): void
    {
        $command = new CreateCommentCommand($this->makeCommentRepository());
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: text');
        $command->handle(new Argument(['postId' => '1','authorId' => '1']));
    }

    public function testItSavesUserToRepository(): void
    {
        $commentRepository = new class implements CommentRepositoryInterface {
        private bool $called = false;
        public function save(Comment $comment): void
        {
            $this->called = true;
        }
        public function get(int $id): Comment
        {
            throw new CommentNotFoundException("Comment with id: $id not found");
        }
        public function wasCalled(): bool
        {
            return $this->called;
        }
        };

        $command = new CreateCommentCommand($commentRepository);

        $command->handle(new Argument([
            'postId' => '1',
            'authorId' => '1',
            'text' => 'testText3',
        ]));

        $this->assertTrue($commentRepository->wasCalled());
    }
}
    