<?php
/**
 * Comments library
 * Contains the loading of specific comments,
 * overall comments, answers and commenting
 * @author  movabo
 */
class Comments {
	
	private $_module;
	private $_identifier;
	private $_CI;

	/**
	 * Magic Method
	 * __construct()
	 *
	 * @param  string module
	 * @param string identifier
	 */
	public function __construct($module, $identifier = NULL)
	{
		$this->_module     = $module[0];
		$this->_identifier = $identifier;
		$this->_CI         =& get_instance();
		$this->_CI->load->model('comments_model');
	}

    /**
	 * Magic Method __call(); Pass-trough to the comments_model
	 * @param string $method Method in the comments_model
	 * @param array $arguments The arguments to pass trough
	 * @return mixed The return of $method-function
	 */
	public function __call($method, $arguments)
	{
		if (!method_exists( $this->_CI->comments_model, $method) )
		{
			throw new Exception('Undefined method Comments::' . $method . '() called');
		}

		return call_user_func_array(array($this->_CI->comments_model, $method), $arguments);
	}


	/**
	 * Setter for identifier
	 * @param string $identifier
	 */
	public function set_identifier($identifier)
	{
		$this->_identifier = $identifier;
	}

	/**
	 * gets comments and answerth (depth: count($limit))
	 * @param  string $order    order of the comments by date
	 * @param  int    $answerTo id of the comment to answer
	 * @param  array  $limit    limit of comments and answers (number of elementh counts also the depth of answers)
	 * @param  array  $offset   offset for the limit
	 * @return array            comments (as objects in array)
	 */
	public function get_comments($order = "DESC", $answerTo = null, $limit = array(30, 5, 2), $offset = array(0, 0, 0))
	{
		if(!isset($offset[0])) $offset[0] = 0;
		$comments = $this->_CI->comments_model->get_comments($this->_module, $this->_identifier, $order, array_shift($limit), array_shift($offset));
		if(count($limit) > 0)
		{
			$c = count($comments);
			for($i = 0; $i < $c; $i++)
			{
				$comments[$i]->answers = $this->get_comments($order, $comments[$i]->id, $limit, $offset);
			}
		}
		return $comments;
	}

	/**
	 * Creates a comment
	 * @param int $userid  id of the user who comments
	 * @param string $text the comment
	 */
	public function comment($userid, $text)
	{
		$this->_CI->comments_model->comment($this->_module, $this->_identifier, $userid, $text);
	}
}