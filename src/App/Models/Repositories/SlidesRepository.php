<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Slide;
use Core\Utils\SurrealDB;

/**
 * Class SlidesRepository
 * @package App\Models\Repositories
 */
class SlidesRepository
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
     * @return Slide[]
     */
    public function all(): array
    {
        $db_res = $this->db->select('*')->tables('slides')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching slides');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return Slide
     */
    public function find(string $id): object
    {
        $db_res = $this->db->select('*')->tables('slides')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching slide');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Slide $content
     * @throws \Exception
     * @return Slide
     */
    public function create(Slide $content): object
    {
        unset($content->id);
        $db_res = $this->db->create('slides')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating slide');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param Slide $content
     * @throws \Exception
     * @return Slide
     */
    public function update(Slide $content): object
    {
        $db_res = $this->db->update($content->id)->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error updating slide');
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
        $db_res = $this->db->delete('slides')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error deleting slide');
        }

        return true;
    }
}
