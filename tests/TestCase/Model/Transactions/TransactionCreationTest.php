<?php
namespace App\Test\TestCase\Controller;

use App\Controller\AppController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

use Model\Transactions\TransactionCreation;

/**
 * App\Controller\DashboardController Test Case
 *
 * @uses \App\Controller\DashboardController
 */
class TransactionCreationTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        
    ];
    
    public function setUp() 
    {
        parent::setUp();
        
    }

    public function testHashingFunction()
    {
        $pairs = [
            "a" => 97,
            "b" => 98,
            "c" => 99,
            "d" => 100,
            "aa" => 12513,
            "ab" => 12514,
            "@"  => 64,
            ".d" => 5988,
            "gmx" => 1701624,
            "@gmx" => 135919352,
            "@gmx.de" => 3742152099,
            "dario.rekowski@gmx.de" => 2928827813,
            "dario.frodo@gmx.de" => 1899591683,
            "theodora.mis@gmx.ch" => 2089074830,
            "maximilian.muster@gradido.net" => 793144931,
            "coin-info5@gradido.net" => 1829129963,
            "coin-info6@gradido.net" => 1830178539,
            "coin-info8@gradido.net" => 1832275691,
            "coin-info9@gradido.net" => 1833324267,
            "coin-info10@gradido.net" => 3877298078,
            "coin-info11@gradido.net" => 3878346654,
            "coin-info12@gradido.net" => 3879395230,
            "theodora.mis@gmx.ch" => 2089074830,
            "waltersurboeck@mac.com" => 3996757473,
            "humanprojects@gmx.de" => 3788634614,
            "roman@be-free.eu" => 807797884,
            "willkommen@mutich.jetzt" => 1640973721,
            "walter.reiner@t-online.de" => 2025729173,
            "andrea.breitenmoser@mutich.jetzt" => 1961122507,
            "claudia.hartfiel@web.de" => 362466358,
            "zeiten-wende-2012@web.de" => 3796728871,
            "roman@be-free.eu" => 807797884,
            "joytopia@gmail.com" => 3794905967,
            "forschungswoche@gestaltungsspielraum.online" => 3077694284,
            "ursulawendeln@web.de" => 3246159770,
            "email@alexandrareis.at" => 3123402690,
            "testneu-11-12-3@gradido.net" => 4092403827,
            "ute.rittervonwaldstatt@hotmail.com" => 3151414199,
            "christine.slotty@web.de" => 3526188273,
            "info@mcmagg.de" => 966804823,
            "markusgollmann.mg@googlemail.com" => 1309273258,
            "ny5@gmx.net" => 995978784,
            "franz@patscheider.com" => 310113324,
            "markusgollmann.mg@googlemail.com" => 1309273258,
            "nconstantin@gmx.ch" => 530108573,
            "manuela.gradido@gmail.com" => 1734855679,
            "r.haenni@haenniag.ch" => 767779182,
            "gabyhug@bluewin.ch" => 2247491519,
            "silvia.hinni@bluewin.ch" => 3248626267,
            "werndl.schechen@gmail.com" => 3516649930,
            "seifritzmoni@bluewin.ch" => 231214190,
            "mohr.carola13@gmail.com" => 4247461928,
            "info@justlife-lebensschule.de" => 324829839,
            "info@wendelin-niederberger.ch" => 3046147747,
            "erhaefliger@bluewin.ch" => 3207307415,
            "h@krueger-online.de" => 728893500,
            "l.mersch@bluemail.ch" => 3905254663,
            "erhaefliger@bluewin.ch" => 3207307415,
            "hahartenberger@t-online.de" => 1155733239,
            "wil@breitenthaler.at" => 2013046423,
            "naturstrom@posteo.de" => 4033835283,
            "f.marchsteiner@aon.at" => 1945541625,
            "haennial@bluewin.ch" => 2310715309,
            "christelann@gmail.com" => 1221362064,
            "margrit-straub@gmx.ch" => 4161339877
        ];
        foreach($pairs as $email => $cpp_hash) {
          $php_hash = TransactionCreation::DRMakeStringHash($email);
          // assertEquals(mixed $expected, mixed $actual[, string $message = ''])
          if($php_hash != $cpp_hash) {
            $this->assertEquals($cpp_hash, $php_hash, "hashes for $email don't match");
          }
        }
    }

    
}
