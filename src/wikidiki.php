<?php 

namespace maciejkrol\wikidiki;

/**
 * WikiDiki class lets you translate words (mostly nouns)
 * across multiple languages based on wikipedia.org
 */
class wikidiki {

    /**
     * Translates the given term from given language.
     * Last parameter can be used to retrieve only
     * selected result or results.
     * @param string $_term
     * @param string $_from
     * @param string|array $_to
     * @return string|array|null
     */
    public function translate ($_term, $_from, $_to = null) {

        $url = $this->buildUrl ($_from, $_term);
        $html = $this->download ($url);

        if ($html === null) {
            return null;
        }

        $results = $this->parseResults ($html);

        if ($_to === null) {
            return $results;
        } else if (is_array ($_to)) {
            $fitlered = [];
            foreach ($_to as $toLang) {
                $fitlered[$toLang] = isset ($results[$toLang]) ? $results[$toLang] : null;
            }
            return $fitlered;
        } else if (isset ($results[$_to])) {
            return $results[$_to];
        } else {
            return null;
        }
    }

    /**
     * Extracts all results from a given HTML.
     * @param string $_html
     * @return array
     */
    private function parseResults ($_html) {
        $regex      = '/(<a href="(.*?)" title="(.*?)" lang="(.*?)" hreflang="(.*?)">(.*?)<\/a>)/i';
        $matches    = [];
        preg_match_all ($regex, $_html, $matches);

        if (count ($matches) === 0) {
            return [];
        }

        $results = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $lang           = $matches[4][$i];
            $href           = $matches[2][$i];
            $translation    = str_replace ($this->buildURL ($lang), '', $href);
            $translation    = $this->cleanTerm ($translation);
            
            if ($translation !== null) {
                $results[$lang] = $translation;
            }
        }

        return $results;
    }

    /**
     * Performs necessary cleaning of an extracted term.
     * Returns null if term is invalid.
     * @param string $_term
     * @return string|null
     */
    private function cleanTerm ($_term) {
        $_term = urldecode ($_term);
        $_term = str_replace ('_', '', $_term);

        return $_term;
    }

    /**
     * Build valid Wikipedia url for a given language and term.
     * @param string $_lang
     * @param string $_term
     * @return string
     */
    private function buildUrl ($_lang, $_term = '') {
        return 'https://'.$_lang.'.wikipedia.org/wiki/'.$_term;
    }

    /**
     * Retrieves html from url. Returns null on error.
     * @param string $_url
     * @return string|null
     */
    private function download ($_url) {
        $curl = curl_init();

    	curl_setopt ($curl, CURLOPT_URL, $_url);
	    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);        
	    curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 5);
	
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code !== 200) {
            $data = null;
        }

    	curl_close($curl);

    	return $data;
    }
}