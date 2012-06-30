<?php

/**
 * A package to use Twilio api https://www.twilio.com.
 *
 * @package    Twilio
 * @version    0.1
 * @author     Matthew McConnell
 * @license    MIT License
 * @copyright  2012 Matthew McConnell
 * @link       http://maca134.co.uk
 */

namespace Twilio;

class Twilio_Request_Recordings extends Twilio_Request implements Twilio_Request_Base {

    protected $defaults = array(
        'Sid' => false,
        'CallSid' => false,
        'DateCreated' => false,
        'Page' => false,
        'PageSize' => 50
    );
    protected $res = '/2010-04-01/Accounts/%s/Recordings%s.json';

    public function create($attr = array()) {
        $sid = (!empty($attr['Sid'])) ? '/' . $attr['Sid'] : '';

        $accoundsid = \Config::get('twilio.account_sid');
        $accoundsid .= (!empty($attr['CallSid'])) ? '/Calls/' . $attr['CallSid'] : '';

        $res = sprintf($this->res, $accoundsid, $sid, '');
        $type = (empty($attr['Sid'])) ? 'list' : 'single';

        unset($attr['Sid'], $attr['CallSid']);

        $body = $this->create_post($attr);
        $response = $this->send($res, $body, 'GET');

        if ($type == 'list' && count($response->recordings)) {
            foreach ($response->recordings as $i => $recording) {
                $response->recordings[$i]->file = str_replace('.json', '.mp3', $recording->uri);
            }
        } else {
            $response->file = str_replace('.json', '.mp3', $response->uri);
        }
        return $response;
    }

    public function delete($sid) {
        $res = sprintf($this->res, \Config::get('twilio.account_sid'), '/' . $sid, '');
        return $this->send($res, '', 'DELETE');
    }

}