<?php
namespace Activity\Lib;

/**
 * Borrowing heavily from https://github.com/piwik/piwik
 * Mad love.
 *
 */
class Excluded
{
    protected $actor;
    protected $action;
    
    public static $UA_Bot_Basics = 'bot|crawl|slurp|spider|transcoder|archiver';
    public static $UA_Bot_Google = 'Google Page Speed Insights|Feedfetcher-Google|Google-Test|Google-Site-Verification|Google Web Preview|Mediapartners-Google|GoogleProducer';
    public static $UA_Bot_Misc = 'aboundex|addthis.com|alertra.com|ia_archiver|curious george|special_archiver|ask jeeves|teoma|backlink-ceck.de|bingpreview|bloglovin|browsershots|butterfly|charlotte|cloudflare|commafeed|email exractor|EmailWolf|ezooms|facebookexternalhit|facebookplatform|feedbin|feedburner|feedly|feedspot|fever|genieo|ichiro|mobile goo|heritrix|httpmon|jigsaw|kouio|linkdex.com|lycos|magpierss|netcraft web server survey|netvibes|newsblur|netresearch|newsgatoronline|PagePeeker|pingdom.com|pompos|scooter|scoutjet|seznam screenshot-generator|shopwiki|silverreader|simplepie|spinn3r|tiny tiny rss|WebThumbnail|w3c|wesee|Yandex(Bot|Images|Antivirus|Direct|Blogs|Favicons|ImageResizer|News(links)?|Metrika|.Gazeta Bot)|yeti|yottaamonitor';
    
    /**
     * Determines if an actor's activity is excluded from tracking
     *
     * @param \Activity\Models\Actors $actor            
     * @return boolean
     */
    public static function actor(\Activity\Models\Actors $actor)
    {
        $excluded = (new static)->setActor($actor)->excludedActor();
        
        return (bool) $excluded;
    }
    
    /**
     * Determines if this kind of request is excluded from tracking
     *
     * @return boolean
     */
    public static function request()
    {
        $excluded = (new static)->excludedRequest();
    
        return (bool) $excluded;
    }    

    /**
     * Determines it the actor is excluded
     * 
     * @return boolean
     */
    public function excludedActor()
    {
        if ($this->isNonHumanBot()) 
        {
            return true;
        }
        
        if ($this->isUAExcluded())
        {
            return true;
        }
        
        if ($this->isIpExcluded())
        {
            return true;
        }        
        
        return false;
    }
    
    /**
     * Has admin specified UAs to exclude?
     * TODO Finish this by adding a settings admin input
     *
     * @return boolean
     */
    public function isUAExcluded()
    {
        return false;
    }    
    
    /**
     * Has admin specified IPs to exclude? 
     * TODO Finish this by adding a settings admin input
     * 
     * @return boolean
     */
    public function isIpExcluded() 
    {
        return false;
    }
    
    /**
     * Live/Bing/MSN bot and Googlebot are evolving to detect cloaked websites.
     * As a result, these sophisticated bots exhibit characteristics of
     * browsers (cookies enabled, executing JavaScript, etc).
     *
     * @see \DeviceDetector\Parser\Bot
     *
     * @return boolean
     */
    protected function isNonHumanBot()
    {
        return ($this->isBotUA() || $this->isBotIp());
    }
    
    /**
     * Evaluates IPs of actor against known bot IPs
     *
     * @return boolean
     */    
    protected function isBotIp()
    {
        $isbot = false;
        
        foreach ($this->getActor()->ips as $ip)
        {
            $isbot = (bool) static::isIpInRange($ip, static::getBotIpRanges());
            if ($isbot)
            {
                break;
            }
        }

        return $isbot;
    }
    
    /**
     * Evaluates User Agent strings of actor against known bot UA strings
     * 
     * @return boolean
     */
    protected function isBotUA()
    {
        $isbot = false;
        
        foreach ($this->getActor()->agents as $agent) 
        {
            $isbot = (bool) preg_match('/('.static::$UA_Bot_Basics.')/i', $agent);
            if ($isbot) 
            {
                break;                
            }
        }
        
        if (!$isbot) 
        {
            foreach ($this->getActor()->agents as $agent)
            {
                $isbot = (bool) preg_match('/('.static::$UA_Bot_Google.')/i', $agent);
                if ($isbot)
                {
                    break;
                }
            }            
        }
        
        if (!$isbot)
        {
            foreach ($this->getActor()->agents as $agent)
            {
                $isbot = (bool) preg_match('/('.static::$UA_Bot_Misc.')/i', $agent);
                if ($isbot)
                {
                    break;
                }
            }
        }        
        
        return $isbot;        
    }
    
    /**
     * See Piwik 
     * 
     * @return multitype:string
     */
    public static function getBotIpRanges()
    {
        return array(
            // Google
            '66.249.0.0/16',
            '64.233.172.0/24',
            // Live/Bing/MSN
            '64.4.0.0/18',
            '65.52.0.0/14',
            '157.54.0.0/15',
            '157.56.0.0/14',
            '157.60.0.0/16',
            '207.46.0.0/16',
            '207.68.128.0/18',
            '207.68.192.0/20',
            '131.253.26.0/20',
            '131.253.24.0/20',
            // Yahoo
            '72.30.198.0/20',
            '72.30.196.0/20',
            '98.137.207.0/20',
            // Chinese bot hammering websites
            '1.202.218.8'
        );
    }    
    
    /**
     * Determines if an IP address is in a specified IP address range.
     *
     * An IPv4-mapped address should be range checked with an IPv4-mapped address range.
     *
     * @param string $ip IP address in network address format
     * @param array $ipRanges List of IP address ranges
     * @return bool  True if in any of the specified IP address ranges; else false.
     */
    public static function isIpInRange($ip, $ipRanges)
    {
        $ipLen = strlen($ip);
        if (empty($ip) || empty($ipRanges) || ($ipLen != 4 && $ipLen != 16)) {
            return false;
        }
    
        foreach ($ipRanges as $range) {
            if (is_array($range)) {
                // already split into low/high IP addresses
                $range[0] = static::P2N($range[0]);
                $range[1] = static::P2N($range[1]);
            } else {
                // expect CIDR format but handle some variations
                $range = static::getIpsForRange($range);
            }
            if ($range === false) {
                continue;
            }
    
            $low = $range[0];
            $high = $range[1];
            if (strlen($low) != $ipLen) {
                continue;
            }
    
            // binary-safe string comparison
            if ($ip >= $low && $ip <= $high) {
                return true;
            }
        }
    
        return false;
    }    
    
    /**
     * Converts an IP address in presentation format to network address format.
     *
     * @param string $ipString IP address, either IPv4 or IPv6, e.g., `"127.0.0.1"`.
     * @return string Binary-safe string, e.g., `"\x7F\x00\x00\x01"`.
     */
    public static function P2N($ipString)
    {
        // use @inet_pton() because it throws an exception and E_WARNING on invalid input
        $ip = @inet_pton($ipString);
        return $ip === false ? "\x00\x00\x00\x00" : $ip;
    }    
    
    /**
     * Determines it the request is excluded
     *
     * @return boolean
     */
    public function excludedRequest()
    {
        if (static::isPrefetchDetected()) 
        {
            return true;
        }
    
        return false;
    }
        

    /**
     * 
     * @return boolean
     */
    public static function isPrefetchDetected()
    {
        return (isset($_SERVER["HTTP_X_PURPOSE"]) && in_array($_SERVER["HTTP_X_PURPOSE"], array(
            "preview",
            "instant"
        ))) || (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == "prefetch");
    }

    /**
     *
     * @param \Activity\Models\Actors $actor
     * @return \Activity\Lib\Excluded
     */
    public function setActor(\Activity\Models\Actors $actor)
    {
        $this->actor = $actor;
    
        return $this;
    }
    
    /**
     * 
     * @return \Activity\Models\Actors
     */
    public function getActor()
    {
        if (!empty($this->actor) && is_a($this->actor, '\Activity\Models\Actors')) 
        {
            return $this->actor;
        }
        
        $actor = new \Activity\Models\Actors;
        return $actor;
    }
    
    /**
     * Gets a key from the DI
     *
     * @param unknown $key            
     */
    public function __get($key)
    {
        return \Dsc\System::instance()->get($key);
    }
}