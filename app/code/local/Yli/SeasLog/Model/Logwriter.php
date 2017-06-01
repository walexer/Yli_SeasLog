<?php
class Yli_SeasLog_Model_Logwriter extends Zend_Log_Writer_Stream
{

    /**
     * The default log priority - for unmapped custom priorities
     * @var string
     */

    protected $_file;

    public function __construct($logFile)
    {
        if(extension_loaded('SeasLog')){
            $logFile = explode(DS, $logFile);
            $this->_file = end($logFile);
        }else{
            parent::__construct($logFile);
        }
    }

    static public function factory($config)
    {
        return new self(self::_parseConfig($config));
    }

    protected function _write($event)
    {
        if(extension_loaded('SeasLog')){
            $priorities = self::priorities();
            if (array_key_exists($event['priority'], $priorities)) {
                $priority = $priorities[$event['priority']];
            } else {
                $priority = self::defaultPriority();
            }

            SeasLog::setBasePath(Mage::getBaseDir('log'));
            SeasLog::log($priority, $this->_file . ' | ' . $event['message']);
        }else{
            return parent::_write($event);
        }
    }

    static protected function priorities()
    {
        $priorities = array(
            Zend_Log::EMERG  => SEASLOG_EMERGENCY,
            Zend_Log::ALERT  => SEASLOG_ALERT,
            Zend_Log::CRIT   => SEASLOG_CRITICAL,
            Zend_Log::ERR    => SEASLOG_ERROR,
            Zend_Log::WARN    => SEASLOG_WARNING,
            Zend_Log::NOTICE => SEASLOG_NOTICE,
            Zend_Log::INFO   => SEASLOG_INFO,
            Zend_Log::DEBUG  => SEASLOG_DEBUG,
        );
        return $priorities;
    }

    static protected function defaultPriority()
    {
        return SEASLOG_NOTICE;
    }

    public function shutdown()
    {
        //do nothing
    }
}
