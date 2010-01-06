<?php

/**
 * BackendPagesModel
 *
 * In this file we store all generic functions that we will be using in the PagesModule
 *
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesModel
{
	/**
	 * Add a number to the string
	 *
	 * @return	string
	 * @param	string $string
	 */
	public static function addNumber($string)
	{
		// split
		$chunks = explode('-', $string);

		// count the chunks
		$count = count($chunks);

		// get last chunk
		$last = $chunks[$count - 1];

		// is nummeric
		if(SpoonFilter::isNumeric($last))
		{
			// remove last chunk
			array_pop($chunks);

			// join together
			$string = implode('-', $chunks ) .'-'. ((int) $last + 1);
		}

		// not numeric
		else $string .= '-2';

		// return
		return $string;
	}


	/**
	 * Build the cache
	 *
	 * @return	void
	 */
	public static function buildCache()
	{
		// get tree
		$levels = self::getTree(array(0));

		// init vars
		$keys = array();
		$navigation = array();

		// @todo	navigation-array isn't correct!

		// loop levels
		foreach($levels as $level => $pages)
		{
			// loop all items on this level
			foreach($pages as $pageID => $page)
			{
				// init var
				$parentID = (int) $page['parent_id'];

				// get url for parent
				$url = (isset($keys[$parentID])) ? $keys[$parentID] : '';

				// add it
				$keys[$pageID] = trim($url .'/'. $page['url'], '/');

				// build navigation array
				$temp = array();
				$temp['page_id'] = $pageID;
				$temp['url'] = $page['url'];
				$temp['full_url'] = $keys[$pageID];
				$temp['title'] = $page['title'];
				$temp['navigation_title'] = $page['navigation_title'];

				// add it
				$navigation['page'][$page['parent_id']][$pageID] = $temp;
			}
		}

		// order by URL
		asort($keys);

		// write the key-file
		$keysString = '<?php' ."\n\n";
		$keysString .= '/**'."\n";
		$keysString .= ' * This file is generated by the Backend, it contains' ."\n";
		$keysString .= ' * the mapping between a pageID and the URL'."\n";
		$keysString .= ' * '."\n";
		$keysString .= ' * @author	Backend'."\n";
		$keysString .= ' * @generated	'. date('Y-m-d H:i:s') ."\n";
		$keysString .= ' */'."\n\n";

		// loop all keys
		foreach($keys as $pageID => $url) $keysString .= '$keys['. $pageID .'] = \''. $url .'\';'."\n";

		// end file
		$keysString .= "\n".'?>';

		// write the file
		SpoonFile::setContent(PATH_WWW .'/frontend/cache/navigation/keys_'. BackendLanguage::getWorkingLanguage() .'.php', $keysString);

		// write the navigation-file
		$navigationString = '<?php' ."\n\n";
		$navigationString .= '/**'."\n";
		$navigationString .= ' * This file is generated by the Backend, it contains' ."\n";
		$navigationString .= ' * more information about the page-structure'."\n";
		$navigationString .= ' * '."\n";
		$navigationString .= ' * @author	Backend'."\n";
		$navigationString .= ' * @generated	'. date('Y-m-d H:i:s') ."\n";
		$navigationString .= ' */'."\n\n";

		// loop all types
		foreach($navigation as $type => $pages)
		{
			// loop all parents
			foreach($pages as $parentID => $page)
			{
				// loop all pages
				foreach($page as $pageID => $properties)
				{
					// loop properties
					foreach($properties as $key => $value)
					{
						// cast properly
						if($key == 'page_id') $value = (int) $value;
						else $value = '\''. $value .'\'';

						// add line
						$navigationString .= '$navigation[\''. $type .'\']['. $parentID .']['. $pageID .'][\''. $key .'\'] = '. $value .';'."\n";
					}

					$navigationString .= "\n";
				}
			}
		}

		// end file
		$navigationString .= '?>';

		// write the file
		SpoonFile::setContent(PATH_WWW .'/frontend/cache/navigation/navigation_'. BackendLanguage::getWorkingLanguage() .'.php', $navigationString);
	}


	/**
	 * Creates the html for the menu
	 *
	 * @return	string
	 * @param	int[optional] $parentId
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $maxDepth
	 * @param	array[optional] $excludedIds
	 * @param	string[optional] $html
	 */
	public static function createHtml($type = 'page', $depth = 0, $parentId = 1, $html = '')
	{
		// require
		require_once PATH_WWW .'/frontend/cache/navigation/navigation_'. BackendLanguage::getWorkingLanguage() .'.php';

		Spoon::dump($navigation[$type][$depth]);

		// check if item exists
		if(isset($navigation[$type][$depth][$parentId]))
		{
			// start html
			$html .= '<ul>' . "\n";

			// loop elements
			foreach($navigation[$type][$depth][$parentId] as $key => $aValue)
			{
				$html .= "\t<li>" . "\n";
				$html .= "\t\t". '<a href="#">'. $aValue['navigation_title'] .'</a>' . "\n";

				// insert recursive here!
				if(isset($navigation[$type][$depth + 1][$key])) $html .= self::createHtml($type, $depth + 1, $parentId, '');

				// add html
				$html .= '</li>' . "\n";
			}

			// end html
			$html .= '</ul>' . "\n";
		}

		// return
		return $html;
	}



	/**
	 * Check if a page exists
	 *
	 * @return	bool
	 * @param	int $id
	 */
	public static function exists($id)
	{
		// redefine
		$id = (int) $id;
		$language = BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get number of rows, if that result is more than 0 it means the page exists
		return (bool) ($db->getNumRows('SELECT p.id
										FROM pages AS p
										WHERE p.id = ? AND p.language = ? AND p.status IN ("active", "draft");',
										array($id, $language)) > 0);
	}


	/**
	 * Get the data for a record
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function get($id)
	{
		// redefine
		$id = (int) $id;
		$language = BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get page (active version)
		return (array) $db->getRecord('SELECT *, UNIX_TIMESTAMP(p.created_on) AS created_on, UNIX_TIMESTAMP(p.edited_on) AS edited_on
										FROM pages AS p
										WHERE p.id = ? AND p.language = ? AND p.status = ?
										LIMIT 1;',
										array($id, $language, 'active'));
	}


	public static function getBlocks($id)
	{
		// redefine
		$id = (int) $id;
		$language = BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get page (active version)
		return (array) $db->retrieve('SELECT pb.*, UNIX_TIMESTAMP(pb.created_on) AS created_on, UNIX_TIMESTAMP(pb.edited_on) AS edited_on
										FROM pages_blocks AS pb
										INNER JOIN pages AS p ON pb.id = p.id
										WHERE p.id = ? AND p.language = ? AND p.status = ?;',
										array($id, $language, 'active'));
	}


	/**
	 * @todo	Get all the available extra's
	 *
	 * @return	array
	 */
	public static function getExtras()
	{
		return array('html' => BL::getLabel('Editor'));
	}


	/**
	 * Get the full-url for a given menuId
	 *
	 * @return	string
	 * @param	int $menuId
	 */
	public static function getFullURL($id)
	{
		// @todo	this method should use a genious caching-system
		// @todo fix me, das bugge code ;)

		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		if($id == 0) return '/';

		$url = (string) $db->getVar('SELECT m.url
										FROM pages AS p
										INNER JOIN meta AS m ON p.meta_id = m.id
										WHERE p.id = ? AND p.status = ?
										LIMIT 1;',
										array($id, 'active'));
	}


	/**
	 * Get the maximum unique id for blocks
	 *
	 * @return	int
	 */
	public static function getMaximumBlockId()
	{
		// get db
		$db = BackendModel::getDB();

		// get the maximum id
		return (int) $db->getVar('SELECT MAX(pb.id)
									FROM pages_blocks AS pb;');
	}


	/**
	 * Get the maximum unique id for pages
	 *
	 * @return	int
	 * @param	string[optional] $language
	 */
	public static function getMaximumMenuId($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get the maximum id
		$maximumMenuId = (int) $db->getVar('SELECT MAX(p.id)
											FROM pages AS p
											WHERE p.language = ?;',
											array($language));

		// pages created by a user should have an id higher then 1000
		// with this hack we can easily find pages added by a user
		if($maximumMenuId < 1000 && !BackendAuthentication::getUser()->isGod()) return $maximumMenuId + 1000;

		// fallback
		return $maximumMenuId;
	}


	/**
	 * Get the maximum sequence inside a leaf
	 *
	 * @return	int
	 * @param	int $parentId
	 * @param	int[optional] $language
	 */
	public static function getMaximumSequence($parentId, $language = null)
	{
		// redefine
		$parentId = (int) $parentId;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// get the maximum sequence inside a certain leaf
		return (int) $db->getVar('SELECT MAX(p.sequence)
									FROM pages AS p
									WHERE p.language = ? AND p.parent_id = ?;',
									array($language, $parentId));
	}


	/**
	 * Get templates
	 *
	 * @return unknown
	 */
	public static function getTemplates()
	{
		// get db
		$db = BackendModel::getDB();

		// get templates
		$templates = (array) $db->retrieve('SELECT t.id, t.label, t.path, t.number_of_blocks, t.is_default, t.data
											FROM pages_templates AS t
											WHERE t.active = ?;',
											array('Y'), 'id');

		// loop templates to unserialize the data
		foreach($templates as $key => $row)
		{
			// unserialize
			$templates[$key]['data'] = unserialize($row['data']);

			// add names into the properties
			$templates[$key]['namesString'] = '"' . implode('", "', $templates[$key]['data']['names']) .'"';
		}

		// return
		return (array) $templates;
	}


	/**
	 * Get all pages/level
	 *
	 * @param	array $ids
	 * @param	array[optional] $data
	 * @param	int[optional] $level
	 * @return	array
	 */
	private static function getTree(array $ids, array $data = null, $level = 1)
	{
		// get db
		$db = BackendModel::getDB();

		$data[$level] = (array) $db->retrieve('SELECT p.id, p.title, p.parent_id, p.navigation_title, p.type,
													m.url
												FROM pages AS p
												INNER JOIN meta AS m ON p.meta_id = m.id
												WHERE p.parent_id IN ('. implode(', ', $ids) .')
												AND p.status = ? AND p.language = ?
												ORDER BY p.sequence ASC;',
												array('active', BackendLanguage::getWorkingLanguage()), 'id');

		// get the childIDs
		$childIds = array_keys($data[$level]);

		// build array
		if(!empty($data[$level])) $data = self::getTree($childIds, $data, ++$level);

		// cleanup
		else unset($data[$level]);

		return $data;
	}


	public static function getTreeHTML()
	{
		$html = '<ul>'."\n";

		$html .= '	<li>';
		// homepage
		$html .= '		<a href="'. BackendModel::createURLForAction('edit', null, null, array('id' => 1)) .'">'. BL::getLabel('Home') .'</a>'."\n";
		$html .= '		<ul>'."\n";
		$html .= '			<a href="'. BackendModel::createURLForAction('edit', null, null, array('id' => 1)) .'">pagina 1</a>'."\n";
		$html .= '		</ul>'."\n";
		$html .= '	</li>'."\n";


		$html .= '	<li>'. BL::getLabel('Footer') .'</li>';
		$html .= '	<li>'. BL::getLabel('Meta') .'</li>';
		$html .= '<ul>';

		return $html;
	}


	/**
	 * @todo	fix me...
	 *
	 * @param unknown_type $url
	 * @param unknown_type $id
	 * @return unknown
	 */
	public static function getUrl($url, $id = null, $parentId = 0)
	{
		// redefine
		$url = (string) $url;
		$parentId = (int) $parentId;

		// get db
		$db = BackendModel::getDB();

		// no specific id
		if($id === null)
		{
			// get number of childs within this parent with the specified url
			$number = (int) $db->getNumRows('SELECT p.id
												FROM pages AS p
												INNER JOIN meta AS m ON p.meta_id = m.id
												WHERE p.parent_id = ? AND  p.status = ? AND m.url = ?;',
												array($parentId, 'active', $url));

			// no items?
			if($number == 0) $url = $url;

			// there are items so, call this method again.
			else
			{
				// add a number
				$url = self::addNumber($url);

				// recall this method, but with a new url
				return self::getUrl($url, $id, $parentId);
			}
		}

		// one item should be ignored
		else
		{
			// get number of childs within this parent with the specified url
			$number = (int) $db->getNumRows('SELECT p.id
												FROM pages AS p
												INNER JOIN meta AS m ON p.meta_id = m.id
												WHERE p.parent_id = ? AND  p.status = ? AND m.url = ? AND p.id != ?;',
												array($parentId, 'active', $url, $id));

			// no items?
			if($number == 0) $url = $url;

			// there are items so, call this method again.
			else
			{
				// add a number
				$url = self::addNumber($url);

				// recall this method, but with a new url
				return self::getUrl($url, $id, $parentId);
			}
		}

		// get full url
		$fullUrl = self::getFullUrl($parentId) .'/'. $url;

		// check if folder exists
		if(SpoonDirectory::exists(PATH_WWW .'/'. $fullUrl))
		{
			// add a number
			$url = self::addNumber($url);

			// recall this method, but with a new url
			return self::getUrl($url, $id, $parentId);
		}

		// check if it is an appliation
		if(in_array(trim($fullUrl, '/'), array_keys(ApplicationRouting::getRoutes())))
		{
			// add a number
			$url = self::addNumber($url);

			// recall this method, but with a new url
			return self::getUrl($url, $id, $parentId);
		}

		// return the unique url!
		return $url;
	}


	/**
	 * Insert a page
	 *
	 * @return	int
	 * @param	array $page
	 */
	public static function insert(array $page)
	{
		// get db
		$db = BackendModel::getDB();

		// insert
		$id = (int) $db->insert('pages', $page);

		// rebuild the cache
		self::buildCache();

		// return the new revision id
		return $id;
	}


	/**
	 * Insert multiple blocks at once
	 *
	 * @return	void
	 * @param	array $blocks
	 */
	public static function insertBlocks(array $blocks)
	{
		// get db
		$db = BackendModel::getDB();

		// insert
		$db->insert('pages_blocks', $blocks);
	}


	public static function update(array $page)
	{
		// get db
		$db = BackendModel::getDB();

		// update old revisions
		$db->update('pages', array('status' => 'archive'), 'id = ?', $page['id']);

		// insert
		$id = (int) $db->insert('pages', $page);

		// @todo	remove revisions that should be removed...

		// return the new revision id
		return $id;
	}


	public static function updateBlocks(array $blocks)
	{
		// get db
		$db = BackendModel::getDB();

		// update old revisions
		$db->update('pages_blocks', array('status' => 'archive'), 'id = ?', $blocks[0]['id']);

		// insert
		$db->insert('pages_blocks', $blocks);

		// @todo	remove revisions that should be removed...
	}

}

?>