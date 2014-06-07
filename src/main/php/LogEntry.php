<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log;
/**
 * Container class for log data.
 */
class LogEntry
{
    /**
     * default seperator between log data fields
     */
    const DEFAULT_SEPERATOR = '|';
    /**
     * seperator between log data fields
     *
     * @type  string
     */
    private $seperator      = self::DEFAULT_SEPERATOR;
    /**
     * logger to which the log data should be send
     *
     * @type  Logger
     */
    private $logger;
    /**
     * target where the log data should go to
     *
     * @type  string
     */
    private $target;
    /**
     * data to log
     *
     * @type  string[]
     */
    private $logData        = [];

    /**
     * constructor
     *
     * How the target is interpreted depends on the log appender which
     * takes the log data. A file log appender might use this as the basename
     * of a file, while a database log appender might use this as the name
     * of the table to write the log data into. Therefore it is advisable to
     * only use ascii characters, numbers and underscores to be sure that the
     * log appender will not mess up the log data.
     *
     * @param  string  $target   target where the log data should go to
     * @param  Logger  $logger   logger to which the log data should be send
     */
    public function __construct($target, Logger $logger)
    {
        $this->target = $target;
        $this->logger = $logger;
    }

    /**
     * returns the target where the log data should go to
     *
     * @return  string
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * returns the target where the log data should go to
     *
     * @return  string
     * @deprecated  since 3.0.0, use target() instead, will be removed with 4.0.0
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * sets the seperator to be used
     *
     * @param   string  $seperator
     * @return  LogEntry
     */
    public function setSeperator($seperator)
    {
        $this->seperator = $seperator;
        return $this;
    }

    /**
     * logs the data using the given logger
     *
     * @api
     */
    public function log()
    {
        $this->logger->log($this);
    }

    /**
     * logs the data delayed using the given logger
     *
     * @since  1.1.0
     * @api
     */
    public function logDelayed()
    {
        $this->logger->logDelayed($this);
    }

    /**
     * adds data to the log object
     *
     * Each call to this method will add a new field. If the data contains line
     * breaks they will be replaced by <nl>. If the data contains the value of
     * the seperator or windows line feeds they will be removed.
     *
     * If the data starts with a double quote but does not end with a double
     * quote a closing double quote will be appended.
     *
     * If the data consists only of a single double quote it will be removed and
     * the added data string will thus be empty.
     *
     * @api
     * @param   string  $data
     * @return  LogEntry
     */
    public function addData($data)
    {
        $this->logData[] = $this->escapeData($data);
        return $this;
    }

    /**
     * helper method to escape given data
     *
     * @param   string  $data
     * @return  string
     */
    private function escapeData($data)
    {
        settype($data, 'string');
        $data = str_replace(chr(13), '', str_replace("\n", '<nl>', $data));
        if (strlen($data) > 1 && '"' === $data{0} && '"' !== $data{(strlen($data) - 1)}) {
            $data .= '"';
        } elseif (strlen($data) == 1 && '"' === $data) {
            $data = '';
        }

        return $data;
    }

    /**
     * replaces data within the log entry
     *
     * If the position to replace does not exist the replacement data will be
     * thrown away. The replacement data will be escaped the same way as when
     * added via addData().
     *
     * @param   int     $position         position to replace
     * @param   string  $replacementData  the data to replace the old data
     * @return  LogEntry
     * @since   1.1.0
     */
    public function replaceData($position, $replacementData)
    {
        if (!isset($this->logData[$position])) {
            return $this;
        }

        $this->logData[$position] = $this->escapeData($replacementData);
        return $this;
    }

    /**
     * returns whole log data
     *
     * @return  string[]
     * @since   1.1.0
     */
    public function data()
    {
        return array_map($this->createEscapeSeperator(), $this->logData);
    }

    /**
     * returns whole log data
     *
     * @return  string[]
     * @since   1.1.0
     * @deprecated  since 3.0.0, use data() instead, will be removed with 4.0.0
     */
    public function getData()
    {
        return $this->data();
    }

    /**
     * returns the whole log data on one line with fields seperated by the seperator
     *
     * @return  string
     */
    public function __toString()
    {
        return join($this->seperator, array_map($this->createEscapeSeperator(), $this->logData));
    }

    /**
     * returns the whole log data on one line with fields seperated by the seperator
     *
     * @return  string
     * @deprecated  since 3.0.0, use __toString() instead, will be removed with 4.0.0
     */
    public function get()
    {
        return $this->__toString();
    }

    /**
     * creates function for escaping a string against seperator, i.e. remove it from data
     *
     * @return  string
     */
    private function createEscapeSeperator()
    {
        return function($data)
               {
                   return str_replace($this->seperator, '', $data);
               };
    }
}
