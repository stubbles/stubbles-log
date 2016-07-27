<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\appender;
use stubbles\log\LogEntry;
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
    private $mailAddress;
    /**
     * name to appear as sender
     *
     * @type  string
     */
    private $senderName;
    /**
     * the collected log data
     *
     * @type  \stubbles\log\LogEntry[]
     */
    private $logEntries  = [];

    /**
     * constructor
     *
     * @param  string  $mailAddress  mail address to send the log data to
     * @param  string  $senderName   optional  name to appear as sender
     */
    public function __construct(string $mailAddress, string $senderName = __CLASS__)
    {
        $this->mailAddress = $mailAddress;
        $this->senderName  = $senderName;
    }

    /**
     * append the log data to the log target
     *
     * @param   \stubbles\log\LogEntry  $logEntry
     * @return  \stubbles\log\appender\MailLogAppender
     */
    public function append(LogEntry $logEntry): LogAppender
    {
        $this->logEntries[] = $logEntry;
        return $this;
    }

    /**
     * finalize the log target
     *
     * @return  \stubbles\log\appender\MailLogAppender
     */
    public function finalize(): LogAppender
    {
        if (count($this->logEntries) === 0) {
            return $this;
        }

        $body = '';
        foreach ($this->logEntries as $logEntry) {
            $body .= $logEntry->target() . ': ' . $logEntry . "\n\n";
        }

        $body .= $this->hostInfo();
        $this->sendMail('Debug message from ' . php_uname('n'), $body);
        return $this;
    }

    /**
     * detects host info when running in web environment
     *
     * @return  string
     */
    private function hostInfo(): string
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            return '';
        }

        $hostInfo = sprintf(
                "\nURL that caused this:\nhttp://%s%s?%s\n",
                $_SERVER['HTTP_HOST'],
                $_SERVER['PHP_SELF'],
                $_SERVER['QUERY_STRING']
        );
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
    protected function sendMail(string $subject, string $body)
    {
        mail(
                $this->mailAddress,
                $subject,
                $body,
                'FROM: ' . $this->senderName . ' <' . $this->mailAddress. '>'
        );
    }
}
