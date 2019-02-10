<?php
namespace DERHANSEN\SfEventMgt\Tests\Unit\ViewHelpers\Registration;

/*
 * This file is part of the Extension "sf_event_mgt" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use DERHANSEN\SfEventMgt\Domain\Model\Registration\Field;
use DERHANSEN\SfEventMgt\ViewHelpers\Registration\IsRequiredFieldViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * Test case for IsRequiredField viewhelper
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class IsRequiredFieldViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var IsRequiredFieldViewHelper
     */
    protected $viewHelper;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = $this->getAccessibleMock(
            IsRequiredFieldViewHelper::class,
            ['renderThenChild', 'renderElseChild']
        );
        $this->viewHelper->expects($this->any())->method('renderThenChild')->will($this->returnValue('then child'));
        $this->viewHelper->expects($this->any())->method('renderElseChild')->will($this->returnValue('else child'));
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
        $this->viewHelper->initializeArguments();
    }

    /**
     * @test
     */
    public function viewHelperDoesNotRenderThenChildWhenNoFieldnameGiven()
    {
        $this->arguments = [
            'fieldname' => '',
            'settings' => [
                'registration' => [
                    'requiredFields' => 'zip'
                ]
            ]
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
        $this->resetSingletonInstances = true;

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('else child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperDoesNotRenderThenChildWhenFieldnameNotInSettings()
    {
        $this->arguments = [
            'fieldname' => 'zip',
            'settings' => [
                'registration' => [
                    'requiredFields' => 'firstname,lastname'
                ]
            ]
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('else child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperRendersThenChildWhenFieldnameInSettings()
    {
        $this->arguments = [
            'fieldname' => 'zip',
            'settings' => [
                'registration' => [
                    'requiredFields' => 'zip,otherfield'
                ]
            ]
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('then child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperRenderThenChildForDefaultRequiredFieldnames()
    {
        $this->arguments = [
            'fieldname' => 'firstname',
            'settings' => [
                'registration' => [
                    'requiredFields' => 'zip,otherfield'
                ]
            ]
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('then child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperDoesNotRenderThenChildWhenNoRegistrationFieldGiven()
    {
        $this->arguments = [
            'registrationField' => null,
            'settings' => []
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('else child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperDoesNotRenderThenChildWhenOptionalRegistrationFieldGiven()
    {
        $optionalRegistrationField = new Field();
        $optionalRegistrationField->setRequired(false);

        $this->arguments = [
            'registrationField' => $optionalRegistrationField,
            'settings' => []
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('else child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperDoesRenderThenChildWhenRequiredRegistrationFieldGiven()
    {
        $optionalRegistrationField = new Field();
        $optionalRegistrationField->setRequired(true);

        $this->arguments = [
            'registrationField' => $optionalRegistrationField,
            'settings' => []
        ];
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->render();
        $this->assertEquals('then child', $actualResult);
    }
}
