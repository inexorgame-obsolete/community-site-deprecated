<?php
/**
 * Inexor's Auth Class
 * User and Group Handling, naming conventions are similliar to J3!
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author 		Lennard "Fohlen" Berger <berger@bnsd.info>
 * @link 		https://github.com/inexor-game/community-site
 * @copyright 	Copyright (c) 2015, Lennard Berger
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Auth
{
	public function __construct()
	{
		$this->load->library(array("database", "PasswordHash"));
	}
	
	/**
	 * Get a user object from the database
	 * Will return a standard user object with empty properties when $id is false/invalid
	 * 
	 * @param integer $id
	 * @return mixed
	 */
	public function getUser($id = false)
	{
		//Use PHP 5.4 function chaining
		$this->db
		->select('*')
		->from('users')
		->join('groups', 'groups.user_id = users.user_id')
		->where('user_id', $id);
		
		$query = $this->db->get();
	
		$user = $query->result(); //instance of stdClass, result_object()
	
		if (empty((array) $user))
		{
			//Preset a few important fields (empty user object)
			$user = new stdClass();
			$user->id = "";
			$user->name = "";
			$user->username = "";
			$user->email = "";
			$user->password = "";
			$user->groups = array();
			$user->status = OFFLINE;
			$user->activation = PENDING;
		}
	
		return $user;
	}
	
	/**
	 * Activates the user if the $activation token matches
	 * 
	 * @param string $activation
	 * @return boolean
	 */
	public function activateUser($activation)
	{
		$this->db
		->select("id")
		->from("activation")
		->where("token", $activation);
		
		$query = $this->db->get();
		
		if ($query->num_fields() > 0)
		{
			$activation = $query->result();
			
			// Update the user's status
			$this->db
			->where("user_id", $activation->user_id)
			->update("users", array("activation" => ACTIVATED));
			
			// Delete the token
			$this->db
			->where("user_id", $activation->user_id)
			->update("activation");
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Set's the User_Status to authenticated
	 *
	 * TODO: Note that we are likely to support more authentication schemas than password later on, which is why I simply use "token" here 
	 * @param integer $id
	 * @param string $token
	 * @return boolean 
	 */
	public function authenticateUser($id, $token)
	{
		$this->db
		->select("password")
		->from("users")
		->where("id", $id);
		
		$query = $this->db->get();
		$password = $query->row();
		
		if ($this->helper->verifyPassword($token, $password))
		{
			$this->db
			->where("user_id", $id)
			->update("users", array("status" => AUTHENTICATED));
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Updates the user password to a new random one in case the $reset token matches
	 * 
	 * @param string $reset
	 * @return boolean
	 */
	public function recoverPassword($reset)
	{
		$this->db
		->select(array("user_id", "password"))
		->from("reset")
		->where("token", $reset);
		
		$query = $this->db->get();
		
		if ($query->num_fields() > 0) {
			$recover = $query->result();
			
			// Update the password record for the user
			$this->db
			->where("id", $recover->user_id)
			->update("users", array("password" => $this->helper->hashPassword($recover->password)));
			
			// Delete the token
			$this->db
			->where("user_id", $recover->user_id)
			->delete("reset");
			
			return true;
		}

		// If no token matches no result is given -> false.
		return false;
	}
	
	/**
	 * Fetch the current user groups for $id
	 * 
	 * @param integer $id
	 * @return array
	 */
	public function getUserGroups($id)
	{
		$this->db
		->select('*')
		->from('groups')
		->where('user_id', $id);
		
		$query = $this->db->get();
		return $query->return_array();
	}
	
	/**
	 * Add a user to one or multiple groups
	 * 
	 * @param integer $id
	 * @param integer $gid
	 * @param array $gid 
	 */
	public function addUserToGroup($id, $gid)
	{
		if (is_array($gid)) {
			$batch = array();
			
			array_walk($gid, function(&$value) use(&$id ,&$batch){
				array_push($batch, array("user_id" => $id, "group_id" => $value));
			});
			
			$this->db->insert_batch('groups', $batch);
		} else {
			$this->db->insert('groups', array("user_id" => $id, "group_id" => $gid));	
		}
	}
	
	/**
	 * Remove one or multiple groups from the user
	 * 
	 * @param integer $id
	 * @param integer $gid
	 * @param array $gid
	 */
	public function removeUserFromGroup($id, $gid)
	{
		if (is_array($gid)) {
			$this->db
			->where_in($gid)
			->delete('groups');
		} else {
			$this->db
			->where(array("user_id" => $id, "group_id" => $gid))
			->delete('groups');
		}		
	}
}