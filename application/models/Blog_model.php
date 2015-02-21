<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Blog_model extends MY_Model
{
	/**
	 * Magic Method __construct();
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load_config('blog');
		$this->load->library('auth');
		$this->_user = $this->auth->user();
	}

	/**
	 * Gets a post by its id 
	 * @param int $id blog-post-id
	 * @return object blog-post
	 */
	public function get_by_id($id)
	{
		$query = $this->db->get_where($this->Table, array('id' => $id));
		return $query->row();
	}

	/**
	 * Gets a post by its slug
	 * @param string $slug The slug (abbreviation) of the post
	 * @return object blog-post
	 */
	public function get_by_slug($slug)
	{
		$query = $this->db->get_where($this->Table, array('slug' => $slug));
		return $query->row();
	}

	/**
	 * Gets multiple posts for a user (so a user can view his own entrys when they are not published)
	 * @param int $userid user-id
	 * @param int $posts posts per site
	 * @param int $start offset
	 * @param bool $is_admin Return non-published posts as well
	 * @return array containing objects of blog-posts
	 */
	public function get_posts_for_user($userid = false, $posts = 10, $start = 0, $is_admin = false)
	{
		if($posts < 0 || $posts === false || $posts != (string) (int) $posts) $posts = 10;
		if($start < 0 || $start === false || $start != (string) (int) $start) $start = 0;
		$this->db->order_by('id', 'desc');
		if($is_admin) $query = $this->db->get($this->Table, $posts, $start);
		else {
			$this->db->where('public', true);
			if($userid !== false) $this->db->or_where('user_id', $userid);
			$query = $this->db->get($this->Table, $posts, $start);
		}
		return $query->result_array();
	}

	/**
	 * Returns how high the maximum pagination according to the posts per site is
	 * @param int $posts posts per site
	 * @param bool $userid user-id
	 * @param bool $is_admin Count non-published posts as well
	 * @return int max-pagination
	 */
	public function max_pagination($posts = 10, $userid = false, $is_admin = false)
	{
		if($posts < 0 || $posts === false || $posts != (string) (int) $posts) $posts = 10;
		$this->db->order_by('id', 'desc');
		if($is_admin) $query = $this->db->get($this->Table);
		else {
			$this->db->where('public', true);
			if($userid !== false) $this->db->or_where('user_id', $userid);
			$query = $this->db->get($this->Table);
		}
		return ceil($query->num_rows() / $posts);
	}

	/**
	 * Inserts a blog post
	 * @param string $headline Headline of the post
	 * @param string $body The body-string (normally serialized)
	 * @param bool $enabled Whether the post is published
	 * @param string $slug The post slug (URL-abbreviation)
	 * @return string slug
	 */
	public function insert($headline, $body, $enabled, $slug = FALSE)
	{
		if($slug == FALSE) $slug = $headline;
		$slug = $this->create_slug($slug);
		$data = array(
			'headline' 	=> $headline,
			'body'		=> $body,
			'user_id'	=> $this->_user->id,
			'timestamp'	=> date('Y-m-d H:i:s'),
			'slug'		=> $slug,
			'public'	=> false
		);
		if($enabled == true) $data['public'] = true;
		$this->db->insert($this->Table, $data);
		$this->db->order_by("id", "DESC");
		return $this->db->get($this->Table)->row()->slug;
	}

	/**
	 * Updates a blog entry
	 * @param int $entryid blog-entry-id
	 * @param array $data the updated data
	 */
	public function update($entryid, $data)
	{
		$this->db->where('id', $entryid);
		$this->db->update($this->Table, $data);
	}

	/**
	 * Creates a slug from a string
	 * @param string $string The string to create the slug from
	 * @return string The slug
	 */
	public function create_slug($string)
	{
		$slug = strip_tags($string);
		$slug = strtolower(str_replace(' ', '-', $slug));
		$slug = preg_replace("/(&[a-z]*;)/", '', $slug); // Remove htmlentities
		$slug = preg_replace("/([^a-z0-9\-]*)/", '', $slug); // Only allow a-z, 0-9 and "-"
		$query = $this->db->get_where($this->Table, array('slug' => $slug));
		if($query->num_rows() > 0)
		{
			$query = $this->db->query(
'SELECT  `AUTO_INCREMENT` 
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA =  ' . $this->db->escape($this->db->database) . '
AND TABLE_NAME = ' . $this->db->escape($this->Table)
			);
			$slug .= '-' . $query->row()->AUTO_INCREMENT;	
		}
		return $slug;
	}
}
