<?php

/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=76; */

/**
 * The editor interior markup, without the header and footer. Broken away
 * from the top-level template so that it can be rendered as a fixture.
 *
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<!-- Editor. -->
<div id="editor"></div>

<!-- Exhibit. -->
<?php echo $this->partial('neatline/_neatline.php', array(
  'exhibit' => $exhibit
)); ?>

<!-- JSON globals. -->
<script type="text/javascript">
  __editor = <?php echo neatline_editorGlobals($exhibit); ?>
</script>

<!-- Templates. -->
<?php echo $this->partial('index/_strings.php'); ?>
<?php echo $this->partial('index/underscore/_record_list.php'); ?>
<?php echo $this->partial('index/underscore/_record_form.php'); ?>
<?php echo $this->partial('index/underscore/_search.php'); ?>
<?php echo $this->partial('index/underscore/_menu.php', array(
  'exhibit' => $exhibit));
?>
