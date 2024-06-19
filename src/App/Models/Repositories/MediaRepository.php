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
     * MediaRepository constructor.
     * @param SurrealDB $db
     */
    public function __construct(SurrealDB $db)
    {
        $this->db = $db;
    }

    /**
     * @return Media[]
     * @throws \Exception
     */
    public function all()
    {
        $db_res = $this->db->select('*')->tables('media')->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching media');
        }

        return $db_res[0]->result;
    }

    /**
     * @param string $id
     * @return Media
     * @throws \Exception
     */
    public function find(string $id)
    {
        $db_res = $this->db->select('*')->tables('media')->where("id = $id")->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error fetching media');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $ref
     * @return Media
     * @throws \Exception
     */
    public function create(Media $content)
    {
        $db_res = $this->db->create('media')->data($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            throw new \Exception('Error creating media');
        }

        $content->id = $db_res[0]->result[0]->id;
        return $content;
    }

    /**
     * @param Media $content
     * @return Media
     * @throws \Exception
     */
    public function update(Media $content)
    {
        $db_res = $this->db->update($content->id)->merge($content)->exec();

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            echo '<pre>';
            print_r($db_res);
            echo '</pre>';

            throw new \Exception('Error updating media');
        }

        return $db_res[0]->result[0];
    }

    /**
     * @param string $id
     * @return bool
     * @throws \Exception
     */
    public function delete(string $id)
    {
        $db_res = $this->db->delete($id)->toSQL() . ';';
        $db_res = $this->db->rawQuery($db_res);

        if (isset($db_res->code) || $db_res[0]->status !== 'OK') {
            echo '<pre>';
            print_r($db_res);
            echo '</pre>';

            throw new \Exception('Error deleting media');
        }

        return true;
    }
}
