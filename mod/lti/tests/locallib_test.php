<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu

/**
 * This file contains unit tests for (some of) lti/locallib.php
 *
 * @package    mod_lti
 * @category   phpunit
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Charles Severance csev@unmich.edu
 * @author     Marc Alier (marc.alier@upc.edu)
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/mod/lti/servicelib.php');


class mod_lti_locallib_testcase extends basic_testcase {

    public function test_split_custom_parameters() {
        $this->assertEquals(lti_split_custom_parameters("x=1\ny=2"),
            array('custom_x' => '1', 'custom_y'=> '2'));

        $this->assertEquals(lti_split_custom_parameters('x=1;y=2'),
            array('custom_x' => '1', 'custom_y'=> '2'));

        $this->assertEquals(lti_split_custom_parameters('Review:Chapter=1.2.56'),
            array('custom_review_chapter' => '1.2.56'));

        $this->assertEquals(lti_split_custom_parameters('Complex!@#$^*(){}[]KEY=Complex!@#$^*(){}[]Value'),
            array('custom_complex____________key' => 'Complex!@#$^*(){}[]Value'));
    }

    /**
     * This test has been disabled because the test-tool is
     * being moved and probably it won't work anymore for this.
     * We should be testing here local stuff only and leave
     * outside-checks to the conformance tests. MDL-30347
     */
    public function disabled_test_sign_parameters() {
        $correct = array ( 'context_id' => '12345', 'context_label' => 'SI124', 'context_title' => 'Social Computing', 'ext_submit' => 'Click Me', 'lti_message_type' => 'basic-lti-launch-request', 'lti_version' => 'LTI-1p0', 'oauth_consumer_key' => 'lmsng.school.edu', 'oauth_nonce' => '47458148e33a8f9dafb888c3684cf476', 'oauth_signature' => 'qWgaBIezihCbeHgcwUy14tZcyDQ=', 'oauth_signature_method' => 'HMAC-SHA1', 'oauth_timestamp' => '1307141660', 'oauth_version' => '1.0', 'resource_link_id' => '123', 'resource_link_title' => 'Weekly Blog', 'roles' => 'Learner', 'tool_consumer_instance_guid' => 'lmsng.school.edu', 'user_id' => '789');

        $requestparams = array('resource_link_id' => '123', 'resource_link_title' => 'Weekly Blog', 'user_id' => '789', 'roles' => 'Learner', 'context_id' => '12345', 'context_label' => 'SI124', 'context_title' => 'Social Computing');

        $parms = lti_sign_parameters($requestparams, 'http://www.imsglobal.org/developer/LTI/tool.php', 'POST',
            'lmsng.school.edu', 'secret', 'Click Me', 'lmsng.school.edu' /*, $org_desc*/);
        $this->assertTrue(isset($parms['oauth_nonce']));
        $this->assertTrue(isset($parms['oauth_signature']));
        $this->assertTrue(isset($parms['oauth_timestamp']));

        // Those things that are hard to mock
        $correct['oauth_nonce'] = $parms['oauth_nonce'];
        $correct['oauth_signature'] = $parms['oauth_signature'];
        $correct['oauth_timestamp'] = $parms['oauth_timestamp'];
        ksort($parms);
        ksort($correct);
        $this->assertEquals($parms, $correct);
    }

    /**
     * This test has been disabled because, since its creation,
     * the sourceId generation has changed and surely this is outdated.
     * Some day these should be replaced by proper tests, but until then
     * conformance tests say this is working. MDL-30347
     */
    public function disabled_test_parse_grade_replace_message() {
        $message = '
            <imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
              <imsx_POXHeader>
                <imsx_POXRequestHeaderInfo>
                  <imsx_version>V1.0</imsx_version>
                  <imsx_messageIdentifier>999998123</imsx_messageIdentifier>
                </imsx_POXRequestHeaderInfo>
              </imsx_POXHeader>
              <imsx_POXBody>
                <replaceResultRequest>
                  <resultRecord>
                    <sourcedGUID>
                      <sourcedId>{&quot;data&quot;:{&quot;instanceid&quot;:&quot;2&quot;,&quot;userid&quot;:&quot;2&quot;},&quot;hash&quot;:&quot;0b5078feab59b9938c333ceaae21d8e003a7b295e43cdf55338445254421076b&quot;}</sourcedId>
                    </sourcedGUID>
                    <result>
                      <resultScore>
                        <language>en-us</language>
                        <textString>0.92</textString>
                      </resultScore>
                    </result>
                  </resultRecord>
                </replaceResultRequest>
              </imsx_POXBody>
            </imsx_POXEnvelopeRequest>
';

        $parsed = lti_parse_grade_replace_message(new SimpleXMLElement($message));

        $this->assertEquals($parsed->userid, '2');
        $this->assertEquals($parsed->instanceid, '2');
        $this->assertEquals($parsed->sourcedidhash, '0b5078feab59b9938c333ceaae21d8e003a7b295e43cdf55338445254421076b');

        $ltiinstance = (object)array('servicesalt' => '4e5fcc06de1d58.44963230');

        lti_verify_sourcedid($ltiinstance, $parsed);
    }
}
