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

<!-- Records. -->
<?php echo $this->partial('index/_records.php', array(
  'exhibit' => $exhibit
)); ?>

<!-- Exhibit. -->
<?php echo $this->partial('neatline/_neatline.php', array(
  'exhibit' => $exhibit
)); ?>

<!-- JSON globals. -->
<script type="text/javascript">
  __editor = <?php echo neatline_editorGlobals($exhibit); ?>
</script>