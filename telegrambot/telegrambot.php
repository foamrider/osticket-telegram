<?php

require_once(INCLUDE_DIR.'class.signal.php');
require_once(INCLUDE_DIR.'class.plugin.php');
require_once('config.php');

class TelegramPlugin extends Plugin {
    var $config_class = "TelegramPluginConfig";

	function bootstrap() {
		Signal::connect('ticket.created', array($this, 'onTicketCreated'), 'Ticket');
    }

	function onTicketCreated($ticket)
	{
		global $ost;
		$ticketLink = $ost->getConfig()->getUrl().'scp/tickets.php?id='.$ticket->getId();
		$ticketId = $ticket->getNumber();
        $title = $ticket->getSubject() ?: 'No subject';
		$createdBy = $ticket->getName()." (".$ticket->getEmail().")";
		$chatid = $this->getConfig()->get('telegram-chat-id');
		
		$this->sendToTelegram(
			array(
				"method" => "sendMessage",
				"chat_id" => $chatid,
				"text" => "*New Ticket:* [#".$ticketId."](".$ticketLink.")\n*Subject:* ".$title."\n*Created by:* ".$createdBy,
				"parse_mode" => "Markdown",
				"disable_web_page_preview" => "True"
			)
		);
	}
	
	function sendToTelegram($payload)
    {
        try {
            global $ost;
     
            $data_string = utf8_encode(json_encode($payload));
            $url = $this->getConfig()->get('telegram-webhook-url');
     
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string)
                )
            );
     
            if (curl_exec($ch) === false) {
                throw new Exception($url . ' - ' . curl_error($ch));
            } else {
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     
                if ($statusCode != '200') {
                    throw new Exception($url . ' Http code: ' . $statusCode);
                }
            }
 
            curl_close($ch);
        } catch(Exception $e) {
            error_log('Error posting to Telegram. '. $e->getMessage());
        }
    }
 
    function escapeText($text)
    {
        $text = str_replace('&', '&amp;', $text);
        $text = str_replace('<', '&lt;', $text);
        $text = str_replace('>', '&gt;', $text);
 
        return $text;
    }
}

