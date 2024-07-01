<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\User;
use App\Models\UserNew;
use Core\Utils\SurrealDB;

/**
 * Class UserRepository
 * @package App\Models\Repositories
 */
class UserRepository
{
    private SurrealDB $db;
    public static array $queries = [];

    /**
     * @param SurrealDB $db
     */
    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Exception
     * @return User[]
     */
    public function all(): array
    {
        $db_res = $this->db->select('*')->tables('users')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching users');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return User
     */
    public function find(string $id): object
    {
        $db_res = $this->db->select('*')->tables('users')->where('id', '=', $id)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching user');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param NewUser $new
     * @throws \Exception
     * @return User
     */
    public function create(UserNew $new): object {}

    /**
     * @param User $user
     * @throws \Exception
     * @return User
     */
    public function update(User $user): object
    {
        /** @var string $user->project */
        $user->project = $user->project->id;

        $db_res = $this->db->update($user->id)->data($user)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating user');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $id
     * @param object $content
     * @throws \Exception
     * @return User
     */
    public function patch(string $id, object $content): object
    {
        echo '<pre>';
        print_r($content);

        $db_res = $this->db->update($id)->merge($content)->exec();

        print_r($db_res);
        echo '</pre>';

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating user');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function delete(string $id): bool
    {
        $db_res = $this->db->delete($id)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error deleting user');
        }

        return true;
    }
}
