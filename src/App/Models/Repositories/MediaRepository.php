<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Media;
use Core\Utils\SurrealDB;

/**
 * Class MediaRepository
 * @package App\Models\Repositories
 */
class MediaRepository
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
     * @return Media[]
     */
    public function all(): array
    {
        $db_res = $this->db->select('*')->tables('media')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching media');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return Media
     */
    public function find(string $id): object
    {
        $db_res = $this->db->select('*')->tables('media')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching media');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $ref
     * @throws \Exception
     * @return Media
     */
    public function create(Media $content): object
    {
        unset($content->id);
        $db_res = $this->db->create('media')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating media');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Media $content
     * @throws \Exception
     * @return Media
     */
    public function update(Media $content): object
    {
        $db_res = $this->db->update($content->id)->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating media');
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
        $sql = $this->db->delete($id)->toSQL() . ';';
        $db_res = $this->db->rawQuery($sql);

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error deleting media');
        }

        return true;
    }
}
