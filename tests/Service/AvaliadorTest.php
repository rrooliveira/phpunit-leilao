<?php

namespace Leilao\Tests\Service;

use Leilao\Model\Lance;
use Leilao\Model\Leilao;
use Leilao\Model\Usuario;
use Leilao\Service\Avaliador;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    /** @var Avaliador  */
    private $avaliador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->avaliador = new Avaliador();
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatoria
     * @param Leilao $leilao
     */
    public function testAvaliadorDeveEncontrarMaiorValorDeLances(Leilao $leilao)
    {
        // Act - When
        $this->avaliador->avalia($leilao);

        $maiorValor = $this->avaliador->getMaiorValor();

        // Assert - Then
        $valorEsperado = 2500;

        self::assertEquals($maiorValor, $valorEsperado);
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatoria
     * @param Leilao $leilao
     */
    public function testAvaliadorDeveEncontrarMenorValorDeLances(Leilao $leilao)
    {
        // Act - When
        $this->avaliador->avalia($leilao);

        $menorValor = $this->avaliador->getMenorValor();

        // Assert - Then
        $valorEsperado = 1700;

        self::assertEquals($menorValor, $valorEsperado);
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatoria
     * @param Leilao $leilao
     */
    public function testAvaliadorDeveEncontrar3MaioresLances(Leilao $leilao)
    {
        // Act - When
        $this->avaliador->avalia($leilao);

        $tresMaioresLances = $this->avaliador->getMaioresLances();

        self::assertCount(3, $tresMaioresLances);
        self::assertEquals(2500, $tresMaioresLances[0]->getValor());
        self::assertEquals(2000, $tresMaioresLances[1]->getValor());
        self::assertEquals(1700, $tresMaioresLances[2]->getValor());
    }

    public function leilaoEmOrdemCrescente()
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('Maria'), 1700));
        $leilao->recebeLance(new Lance(new Usuario('João'), 2000));
        $leilao->recebeLance(new Lance(new Usuario('José'), 2500));

        return [
           'Leilão em ordem crescente' => [$leilao]
        ];
    }

    public function leilaoEmOrdemDecrescente()
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('José'), 2500));
        $leilao->recebeLance(new Lance(new Usuario('João'), 2000));
        $leilao->recebeLance(new Lance(new Usuario('Maria'), 1700));

        return [
            'Leilão em ordem decrescente' => [$leilao]
        ];
    }

    public function leilaoEmOrdemAleatoria()
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('João'), 2000));
        $leilao->recebeLance(new Lance(new Usuario('José'), 2500));
        $leilao->recebeLance(new Lance(new Usuario('Maria'), 1700));

        return [
            'Leilão em ordem aleatória' => [$leilao]
        ];
    }

    public function testLeilaoSemLancesNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar um leilão que não possui lances.');

        $leilao = new Leilao('Passat 0KM');
        $this->avaliador->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O leilão está finalizado e não pode ser avaliado.');

        $leilao = new Leilao('Honda Fit 0KM');
        $leilao->recebeLance(new Lance(new Usuario('João'), 6000));
        $leilao->finalizaLeilao();
        $this->avaliador->avalia($leilao);
    }
}