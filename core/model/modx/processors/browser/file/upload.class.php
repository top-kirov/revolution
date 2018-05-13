<?php
/**
 * Upload files to a directory
 *
 * @param string $path The target directory
 *
 * @package modx
 * @subpackage processors.browser.file
 */
class modBrowserFileUploadProcessor extends modProcessor {
    /** @var modMediaSource $source */
    public $source;
    public function checkPermissions() {
        return $this->modx->hasPermission('file_upload');
    }

    public function getLanguageTopics() {
        return array('file');
    }

    public function initialize() {
        $this->setDefaultProperties(array(
            'source' => 1,
            'path' => false,
        ));
        if (!$this->getProperty('path')) return $this->modx->lexicon('file_folder_err_ns');
        return true;
    }

    public function process() {
        if (!$this->getSource()) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        if (!$this->source->checkPolicy('create')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }

        $path = preg_replace('/[\.]{2,}/', '', htmlspecialchars($this->getProperty('path')));
        ////////////////////////////////////////////////////////
        if($this->getProperty('create_folders'))
        {
        	$bases = $this->source->getBases($path);
        	$fullPath = $bases['pathAbsolute'].ltrim($path,'/');
        	if(!file_exists($fullPath))mkdir($fullPath,$this->modx->getOption('new_folder_permissions',null,0775),true);
        	//$fullpath = $this->source->
        }
        if($path[strlen($path)-1]!=='/')$path.='/';
        ///////////////////////////////////////////////////////
        $success = $this->source->uploadObjectsToContainer($path,$_FILES);

        if (empty($success)) {
            $msg = '';
            $errors = $this->source->getErrors();
            foreach ($errors as $k => $msg) {
                $this->modx->error->addField($k,$msg);
            }
            return $this->failure($msg);
        }
        
        /////////////////////////////////////////////////////////
        $success = $this->success();
        $success['object'] = $this->source->uploaded_objects;
        return $success;
        /////////////////////////////////////////////////////////
    }

    /**
     * Get the active Source
     * @return modMediaSource|boolean
     */
    public function getSource() {
        $this->modx->loadClass('sources.modMediaSource');
        $this->source = modMediaSource::getDefaultSource($this->modx,$this->getProperty('source'));
        if (empty($this->source) || !$this->source->getWorkingContext()) {
            return false;
        }
        return $this->source;
    }
}
return 'modBrowserFileUploadProcessor';
