<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\appender;
use net\stubbles\log\LogEntry;
/**
 * A log appenders that sends log data to a mail address.
 *
 * This log appender writes the log data into a mail which will be send to
 * the configured mail address.
 */
class MailLogAppender implements LogAppender
{
    /**
     * mail address to send the log data to
     *
     * @type  string
     */
    protected $mailAddress;
    /**
     * name to appear as sender
     *
     * @type  string
     */
    protected $senderName;
    /**
     * the collected log data
     *
     * @type  LogEntry[]
     */
    protected $logEntries  = array();

    /**
     * constructor
     *
     * @param  string  $mailAddress  mail address to send the log data to
     * @param  string  $senderName   optional  name to appear as sender
     */
    public function __construct($mailAddress, $senderName = __CLASS__)
    {
        $this->mailAddress = $mailAddress;
        $this->senderName  = $senderName;
    }

    /**
     * append the log data to the log target
     *
     * @param   LogEntry  $logEntry
     * @return  MailLogAppender
     */
    public function append(LogEntry $logEntry)
    {
        $this->logEntries[] = $logEntry;
        return $this;
    }

    /**
     * finalize the log target
     *
     * @return  MailLogAppender
     */
    public function finalize()
    {
        if (count($this->logEntries) === 0) {
            return;
        }

        $body = '';
        foreach ($this->logEntries as $logEntry) {
            $body .= $logEntry->getTarget() . ': ' . $logEntry->get() . "\n\n";
        }

        $body .= $this->getHostInfo();
        $this->sendMail('Debug message from ' . php_uname('n'), $body);
        return $this;
    }

    /**
     * detects host info when running in web environment
     *
     * @return  string
     */
    protected function getHostInfo()
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            return '';
        }

        $hostInfo = sprintf("\nURL that caused this:\nhttp://%s%s?%s\n", $_SERVER['HTTP_HOST'], $_SERVER['PHP_SELF'], $_SERVER['QUERY_STRING']);
        if (isset($_SERVER['HTTP_REFERER'])) {
            $hostInfo .= sprintf("\nReferer:\n%s\n", $_SERVER['HTTP_REFERER']);
        }

        return $hostInfo;
    }

    /**
     * sends the mail
     *
     * @param  string  $subject  subject of the mail to send
     * @param  string  $body     body of the mail to send
     */
    protected function sendMail($subject, $body)
    {
        mail($this->mailAddress, $subject, $body, 'FROM: ' . $this->senderName . ' <' . $this->mailAddress. '>');
    }
}
?>