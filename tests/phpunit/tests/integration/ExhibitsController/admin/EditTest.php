<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class ExhibitsControllerTest_AdminEdit extends Neatline_Case_Default
{


    public function setUp()
    {
        parent::setUp();
        $this->_mockPresenters();
        $this->_mockExhibitWidgets();
        $this->_mockLayers();
    }


    /**
     * EDIT should display the exhibit edit form.
     */
    public function testBaseMarkup()
    {

        $exhibit = $this->_exhibit('slug');
        $imagePath = NL_TEST_DIR.'/mocks/image.jpg';

        $exhibit->setArray(array(
            'title'             => 'Title',
            'narrative'         => 'Narrative.',
            'widgets'           => 'Widget1,Widget2',
            'spatial_layers'    => 'Layer1,Layer3',
            'spatial_layer'     => 'Layer3',
            'image_layer'       => $imagePath,
            'wms_address'       => 'wms.org',
            'wms_layers'        => 'wms:layer',
            'zoom_levels'       => 10,
            'spatial_querying'  => 1,
            'public'            => 1
        ));

        $exhibit->save();

        $this->dispatch('neatline/edit/'.$exhibit->id);

        // Title:
        $this->assertXpath(
            '//input[@name="title"][@value="Title"]'
        );

        // Slug:
        $this->assertXpath(
            '//input[@name="slug"][@value="slug"]'
        );

        // Narrative:
        $this->assertXpathContentContains(
            '//textarea[@name="narrative"]',
            'Narrative.'
        );

        // Widgets:
        $this->assertXpath(
            '//select[@name="widgets[]"]/
            option[@selected="selected"][@value="Widget1"]'
        );
        $this->assertXpath(
            '//select[@name="widgets[]"]/
            option[@selected="selected"][@value="Widget2"]'
        );

        // Spatial Layers:
        $this->assertXpath(
            '//select[@name="spatial_layers[]"]/optgroup/
            option[@selected="selected"][@value="Layer1"]'
        );
        $this->assertXpath(
            '//select[@name="spatial_layers[]"]/optgroup/
            option[@selected="selected"][@value="Layer3"]'
        );

        // Spatial Layer:
        $this->assertXpath(
            '//select[@name="spatial_layer"]/optgroup/
            option[@selected="selected"][@value="Layer3"]'
        );

        // Image Layer:
        $this->assertXpath(
            "//input[@name='image_layer'][@value='$imagePath']"
        );

        // Zoom Levels:
        $this->assertXpath(
            '//input[@name="zoom_levels"][@value="10"]'
        );

        // WMS Address:
        $this->assertXpath(
            '//input[@name="wms_address"][@value="wms.org"]'
        );

        // WMS Layer:
        $this->assertXpath(
            '//input[@name="wms_layers"][@value="wms:layer"]'
        );

        // Spatial Querying:
        $this->assertXpath(
            '//input[@name="spatial_querying"][@checked="checked"]'
        );

        // Public:
        $this->assertXpath(
            '//input[@name="public"][@checked="checked"]'
        );

    }


    /**
     * A title is required.
     */
    public function testNoTitleError()
    {

        $exhibit = $this->_exhibit();

        // Missing title.
        $this->request->setMethod('POST')->setPost(array(
            'title'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="title"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * A slug is required.
     */
    public function testNoSlugError()
    {

        $exhibit = $this->_exhibit();

        // Missing slug:
        $this->request->setMethod('POST')->setPost(array(
            'slug'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="slug"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * The slug cannot have spaces.
     */
    public function testInvalidSlugWithSpacesError()
    {

        $exhibit = $this->_exhibit();

        // Spaces:
        $this->request->setMethod('POST')->setPost(array(
            'slug' => 'slug with spaces'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="slug"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * The slug cannot have capitals.
     */
    public function testInvalidSlugWithCapsError()
    {

        $exhibit = $this->_exhibit();

        // Capitals:
        $this->request->setMethod('POST')->setPost(array(
            'slug' => 'Slug-With-Capitals'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="slug"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * The slug cannot have non-alphanumeric characters.
     */
    public function testInvalidSlugWithNonAlphasError()
    {

        $exhibit = $this->_exhibit();

        // Non-alphanumerics:
        $this->request->setMethod('POST')->setPost(array(
            'slug' => 'slug#with%non&alphas!'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="slug"]/
            following-sibling::ul[@class="error"]'
        );

    }

    /**
     * The slug cannot be the same as the slug for another exhibit.
     */
    public function testDuplicateSlugError()
    {

        $exhibit1 = $this->_exhibit('slug-1');
        $exhibit2 = $this->_exhibit('slug-2');

        // Duplicate slug.
        $this->request->setMethod('POST')->setPost(array(
            'slug' => 'slug-2'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit1->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="slug"]/
            following-sibling::ul[@class="error"]'
        );

    }

    /**
     * The new slug can be the same as the current slug.
     */
    public function testUnchangedSlug()
    {

        $exhibit = $this->_exhibit();

        // Unchanged slug.
        $this->request->setMethod('POST')->setPost(array(
            'title'           => 'title',
            'slug'            => 'slug',
            'spatial_layers'  => array('Layer1', 'Layer2'),
            'spatial_layer'   => 'Layer2',
            'widgets'         => array('Widget1', 'Widget2'),
            'zoom_levels'     => '50',
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);

        // Should save exhibit.
        $this->assertEquals('title', $this->_reload($exhibit)->title);

    }


    /**
     * A default base layer must be selected.
     */
    public function testNoDefaultLayersError()
    {

        $exhibit = $this->_exhibit();

        // Missing title.
        $this->request->setMethod('POST')->setPost(array(
            'spatial_layer'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//select[@name="spatial_layer"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * A zoom level count must be provided.
     */
    public function testNoZoomLevelsError()
    {

        $exhibit = $this->_exhibit();

        // Missing zoom level count.
        $this->request->setMethod('POST')->setPost(array(
            'zoom_levels'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="zoom_levels"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * The zoom level count can't contain non-digits.
     */
    public function testAlphaZoomLevelsError()
    {

        $exhibit = $this->_exhibit();

        // Missing zoom level count.
        $this->request->setMethod('POST')->setPost(array(
            'zoom_levels' => 'alpha'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="zoom_levels"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * The zoom level count can't contain decimals.
     */
    public function testDecimalZoomLevelsError()
    {

        $exhibit = $this->_exhibit();

        // Missing zoom level count.
        $this->request->setMethod('POST')->setPost(array(
            'zoom_levels' => '50.5'
        ));

        // Submit the form.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $this->assertAction('edit');

        // Should flash error.
        $this->assertXpath('//input[@name="zoom_levels"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * When all fields are valid, the exhibit should be updated.
     */
    public function testSuccess()
    {

        $exhibit = $this->_exhibit();
        $imagePath = NL_TEST_DIR.'/mocks/image.jpg';

        $this->request->setMethod('POST')->setPost(array(
            'title'             => 'Title',
            'slug'              => 'slug',
            'narrative'         => 'Narrative.',
            'widgets'           => array('Widget1', 'Widget2'),
            'spatial_layers'    => array('Layer1', 'Layer2'),
            'spatial_layer'     => 'Layer1',
            'image_layer'       => $imagePath,
            'zoom_levels'       => '50',
            'wms_address'       => 'wms.org',
            'wms_layers'        => 'wms:layer',
            'spatial_querying'  => 1,
            'public'            => 1
        ));

        // Submit the form, _reload exhibit.
        $this->dispatch('neatline/edit/'.$exhibit->id);
        $exhibit = $this->_reload($exhibit);

        // Should set fields.
        $this->assertEquals('Title',            $exhibit->title);
        $this->assertEquals('slug',             $exhibit->slug);
        $this->assertEquals('Narrative.',       $exhibit->narrative);
        $this->assertEquals('Widget1,Widget2',  $exhibit->widgets);
        $this->assertEquals('Layer1,Layer2',    $exhibit->spatial_layers);
        $this->assertEquals('Layer1',           $exhibit->spatial_layer);
        $this->assertEquals($imagePath,         $exhibit->image_layer);
        $this->assertEquals(50,                 $exhibit->zoom_levels);
        $this->assertEquals('wms.org',          $exhibit->wms_address);
        $this->assertEquals('wms:layer',        $exhibit->wms_layers);
        $this->assertEquals(1,                  $exhibit->spatial_querying);
        $this->assertEquals(1,                  $exhibit->public);

    }


}