<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model class that wraps table problem
 *
 * This is the active record class wrapper for table 'problem' from the database.
 * 
 * @package models
 * @author  Ashar Fuadi <fushar@gmail.com>
 */
class Problem_manager extends AR_Model 
{
	/**
	 * Constructs a new model object
	 *
	 * This function constructs a new model.
	 */
	public function __construct()
	{
		parent::__construct('problem');
	}

	/**
	 * Inserts a new row into the table
	 *
	 * This function inserts a new row described by $attributes into the wrapped table. If a certain attribute is not
	 * present in $attributes, its default value will be inserted instead.
	 *
	 * This function overrides AR_Model::insert_row(). It will also create the directory for the problem's testcases.
	 * 
	 * @param  array $attributes The row to be inserted as an array of <attribute name> => <attribute value> pairs.
	 * 
	 * @return int               The newly inserted row ID.
	 */
	public function insert_row($attributes)
	{
		$insert_id = parent::insert_row($attributes);
		$testcase_path = $this->setting->get('testcase_path') . '/' . $insert_id;
		if ( ! is_dir($testcase_path))
		{
			mkdir($testcase_path);
			chmod($testcase_path, 0777);
		}
		return $insert_id;
	}

	/**
	 * Retrieves zero or more rows from table 'problem' satisfying some criteria
	 *
	 * This function returns zero or more rows from the wrapped table that satisfy the given criteria. All attributes
	 * are returned.
	 *
	 * This function overrides AR_Model::get_rows(). It can also accept 'contest_id' as a criteria key; i.e., the
	 * contest IDs that this problem is assigned.
	 * 
	 * @param  array $criteria   An array of <attribute-name> => <attribute-value> pairs. Only rows that satisfy all
	 *                           criteria will be retrieved.
	 * @param  array $conditions An array of zero or more of the following pairs:
	 *                           - 'limit'    => The maximum number of rows to be retrieved.
	 *                           - 'offset'   => The starting number of the rows to be retrieved.
	 *                           - 'order_by' => The resulting rows order as an array of zero or more
	 *                                           <attribute-name> => ('ASC' | 'DESC') pairs.
	 * 
	 * @return array             Zero or more rows that satisfies the criteria as an array of <row-ID> => <row>.
	 */
	public function get_rows($criteria = array(), $conditions = array())
	{
		if (isset($criteria['contest_id']))
		{
			$this->db->select('id, name, author, time_limit, memory_limit, alias');
			$this->db->join('contest_problem', 'contest_problem.problem_id=id');
			$this->db->where('contest_problem.contest_id', $criteria['contest_id']);
		}
		else
		{
			$this->db->select('problem.id AS id, name, author, time_limit, memory_limit, COUNT(testcase.id) AS tc_count');
			$this->db->join('testcase', 'testcase.problem_id=problem.id', 'left outer');
			$this->db->group_by('problem.id');
		}
		
		return parent::get_rows($criteria, $conditions);
	}

	/**
	 * Adds a new testcase for a particular problem
	 *
	 * This function adds a new test case with input file $args['input'] and output file $args['output'] to the problem
	 * whose ID is $args['problem_id'].
	 * 
	 * @param  array $args The parameter.
	 * 
	 * @return string An empty string if there is no error, or the current language representation of:
	 * 				  - 'input_output_must_differ'
	 *                - 'input_has_been_used'
	 * 				  - 'output_has_been_used'
	 */
	public function add_testcase($args)
	{
		if ($args['input'] == $args['output'])
			return $this->lang->line('input_output_must_differ');

		$q = $this->db->query('SELECT * FROM testcase WHERE problem_id=' . $args['problem_id'] . ' AND (input="' . $args['input'] . '" OR output="' . $args['input'] . '")');
		if ($q->num_rows() > 0)
			return $this->lang->line('input_has_been_used');

		$q = $this->db->query('SELECT * FROM testcase WHERE problem_id=' . $args['problem_id'] . ' AND (input="' . $args['output'] . '" OR output="' . $args['output'] . '")');
		if ($q->num_rows() > 0)
			return $this->lang->line('output_has_been_used');

		$testcase_path = $this->setting->get('testcase_path') . '/' . $args['problem_id'];
		
		$this->load->library('upload');
		$this->load->library('unzip');

		$config['upload_path'] = $testcase_path;
		$config['allowed_types'] = '*';
		$config['file_name'] = $args['input'];
		$config['remove_spaces'] = FALSE;

		$this->upload->initialize($config);
		$this->upload->do_upload('new_input');
		$this->unzip->extract($testcase_path . '/' . $args['input']);

		if ($this->upload->display_errors() != '')
			return $this->upload->display_errors();

		$config['upload_path'] = $testcase_path;
		$config['allowed_types'] = 'zip';
		$config['file_name'] = $args['output'];
		$config['remove_spaces'] = FALSE;

		$joot = preg_replace('/_/','',$args['output']);

		$this->upload->initialize($config);
		$this->upload->do_upload('new_output');
		$this->unzip->extract($testcase_path . '/' . $joot);

		if ($this->upload->display_errors() != '')
			return $this->upload->display_errors();

		
		list($filepartin, $wastein, $zipin) = explode('.', $args['input']);
		list($filepartout, $wasteout, $zipout) = explode('.', $args['output']);


		for($i = 1; $i <= 10; $i++)
		
		{
			$fileinputname = $filepartin . "." . $i . ".in";
			$fileoutputname = $filepartout . ".". $i . ".out";

			$this->db->set('problem_id', $args['problem_id']);
			$this->db->set('input', $fileinputname);
			$this->db->set('input_size', filesize($testcase_path . '/' . $fileinputname));
			$this->db->set('output', $fileoutputname);
			$this->db->set('output_size', filesize($testcase_path . '/' . $fileoutputname));
			$this->db->insert('testcase');
		}
		return '';

	}

	/**
	 * Deletes a particular testcase from a particular problem
	 *
	 * This function removes the testcase whose ID is $testcase_id.
	 * 
	 * @param int $testcase_id The testcase ID.
	 */
	public function delete_testcase($testcase_id)
	{
		$this->db->from('testcase');
		$this->db->where('id', $testcase_id);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() == 0)
			return;
		$res = $q->row_array();

		$this->db->where('id', $testcase_id);
		$this->db->delete('testcase');

		$testcase_path = $this->setting->get('testcase_path') . '/' . $res['problem_id'];
		unlink($testcase_path . '/' . $res['input']);
		unlink($testcase_path . '/' . $res['output']);
	}

	/**
	 * Retrieves the testcases for a particular problem
	 *
	 * This function returns the testcases for the problem whose ID is $problem_id.
	 * 
	 * @param int $problem_id The problem ID.
	 *
	 * @return aray The testcases.
	 */
	public function get_testcases($problem_id)
	{
		$this->db->from('testcase');
		$this->db->where('problem_id', $problem_id);
		$q = $this->db->get();
		return $q->result_array();
	}

	/**
	 * Retrieves a particular testcase
	 *
	 * This function returns the testcase whose ID is $testcase_id.
	 * 
	 * @param int $testcase_id The testcase ID.
	 *
	 * @return aray The testcase.
	 */
	public function get_testcase($testcase_id)
	{
		$this->db->from('testcase');
		$this->db->where('id', $testcase_id);
		$q = $this->db->get();
		if ($q->num_rows() == 0)
			return FALSE;
		return $q->row_array();
	}

	/**
	 * Retrieves a particular testcase file content
	 *
	 * This function returns the content of the testcase whose ID is $testcase_id.
	 *
	 * 
	 * @param int $testcase_id The testcase ID.
	 *
	 * @return string - If $direction is 'in', then the content of the input file will be returned.
	 *                - If $direction is 'out', then the content of the output file will be returned.
	 *                - Otherwise, an empty string is returned.
	 */
	public function get_testcase_content($testcase_id, $direction)
	{
		$this->db->select('problem_id, ' . $direction);
		$this->db->from('testcase');
		$this->db->where('id', $testcase_id);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() == 0)
			return '';
		$res = $q->row_array();

		$testcase_path = $this->setting->get('testcase_path') . '/' . $res['problem_id'];
		return file_get_contents($testcase_path . '/' . $res[$direction]);
	}
}

/* End of file problem_manager.php */
/* Location: ./application/models/contestant/problem_manager.php */
