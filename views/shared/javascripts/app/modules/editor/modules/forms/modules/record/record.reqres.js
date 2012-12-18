
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * Form request/response handlers.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Neatline.module('Editor.Forms.Record', function(
  Record, Forms, Backbone, Marionette, $, _) {


  /**
   * Is the form currently open?
   *
   * @return {Boolean}: True if a form is open.
   */
  Neatline.reqres.addHandler('editor:form:isOpen?', function() {
    return Record.view.open;
  });


});