<?php

class Model_DbTable_Albums extends Zend_Db_Table_Abstract
{
	protected $_name = 'albums';
	
	public function getAlbum($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('id = ' . $id);
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function addAlbum($artist, $title)
	{
		$data = array('artist' => $artist, 'album' => $title);
		$this->insert($data);
	}

	public function updateAlbum($id, $artist, $album)
	{
		$data = array('artist' => $artist, 'album' =>  $album);
		$this->update($data, 'id = ' . (int)$id);
	}

	public function deleteAlbum($id)
	{
		$this->delete('id = ' . (int)$id);
	}
}