<?php

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class ItemsControllerTest_Get extends Neatline_Case_Default
{


    public function setUp()
    {
        parent::setUp();
        $this->_mockTheme();
    }


    /**
     * When a record is specified that matches a tag-specific template in the
     * custom exhibit theme, GET should apply the tag template.
     */
    public function testRecordTagTemplate()
    {

        $exhibit    = $this->_exhibit('custom');
        $item       = $this->_item();
        $record     = $this->_record($exhibit, $item);

        // Assign `tag1`.
        $record->tags = 'tag1';
        $record->save();

        // Load the item body.
        $this->request->setQuery(array('record' => $record->id));
        $this->dispatch('neatline/items/'.$item->id);

        // Should render the tag template.
        $body = trim($this->_getResponseBody());
        $this->assertEquals("custom-{$item->id}-tag1", $body);

    }


    /**
     * When a record is specified that matches a exhibit-generic template in
     * the custom exhibit theme, GET should apply the exhibit template.
     */
    public function testExhibitSlugTemplate()
    {

        $exhibit    = $this->_exhibit('custom');
        $item       = $this->_item();
        $record     = $this->_record($exhibit, $item);

        // Load the item body.
        $this->request->setQuery(array('record' => $record->id));
        $this->dispatch('neatline/items/'.$item->id);

        // Should render the exhibit template.
        $body = trim($this->_getResponseBody());
        $this->assertEquals("custom-{$item->id}", $body);

    }


    /**
     * When a record is specified that doesn't match any custom templates, GET
     * should render the default template.
     */
    public function testDefaultItemTemplate()
    {

        $exhibit    = $this->_exhibit('no-custom-theme');
        $item       = $this->_item();
        $record     = $this->_record($exhibit, $item);

        // Load the item body.
        $this->request->setQuery(array('record' => $record->id));
        $this->dispatch('neatline/items/'.$item->id);

        echo "OMEKA_DIR   = " . OMEKA_DIR   . "\n";
        echo "NL_DIR      = " . NL_DIR      . "\n";
        echo "NL_TEST_DIR = " . NL_TEST_DIR . "\n";
        if (file_exists(NL_TEST_DIR . '/mocks/theme/neatline/exhibits/item.php')) {
            echo "ITEM TEMPLATE EXISTS.\n";
        } else {
            echo "ITEM TEMPLATE MISSING.\n";
        }
        foreach (get_view()->getScriptPaths() as $p) {
            echo "SCRIPT PATH = <$p> ";
            if (file_exists($p . '/exhibits/item.php')) {
                echo "FOUND\n";
            } else {
                echo "NOT FOUND\n";
            }
        }

        // Should render the default template.
        $body = trim($this->_getResponseBody());
        $this->assertEquals($item->id, $body);
        //$this->assertQueryContentContains('#dublin-core-subject p', '[no text]');

    }


    /**
     * When a record isn't specified , GET should render the default template.
     */
    public function testNoRecordPassed()
    {

        $item = $this->_item();

        // No record specified.
        $this->dispatch('neatline/items/'.$item->id);

        // Should render the default template.
        $body = trim($this->_getResponseBody());
        $this->assertEquals($item->id, $body);
        //$this->assertQueryContentContains('#dublin-core-subject p', '[no text]');
    }


}
