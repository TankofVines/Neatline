<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * Tests for `deleteTag()` on NeatlineRecordTable.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Neatline_NeatlineRecordTableTest_DeleteTag
    extends Neatline_Test_AppTestCase
{


    /**
     * --------------------------------------------------------------------
     * deleteTag() should remove all instances of a given tag in the tags
     * field on data records.
     * --------------------------------------------------------------------
     */
    public function testDeleteTag()
    {

        // Create exhibit.
        $exhibit = $this->__exhibit();

        // Create record.
        $record = new NeatlineRecord($exhibit);
        $record->tags = 'tag1,tag2,tag3,tag4';
        $record->save();

        // Delete `tag2`, reload record, check string.
        $this->_recordsTable->deleteTag($exhibit, 'tag2');
        $record = $this->_recordsTable->find($record->id);
        $this->assertEquals($record->tags, 'tag1,tag3,tag4');

        // Delete `tag1`, reload record, check string.
        $this->_recordsTable->deleteTag($exhibit, 'tag1');
        $record = $this->_recordsTable->find($record->id);
        $this->assertEquals($record->tags, 'tag3,tag4');

        // Delete `tag4`, reload record, check string.
        $this->_recordsTable->deleteTag($exhibit, 'tag4');
        $record = $this->_recordsTable->find($record->id);
        $this->assertEquals($record->tags, 'tag3');

        // Delete `tag3`, reload record, check string.
        $this->_recordsTable->deleteTag($exhibit, 'tag3');
        $record = $this->_recordsTable->find($record->id);
        $this->assertEquals($record->tags, '');

    }


}