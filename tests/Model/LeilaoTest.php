<?php

namespace Leilao\Tests\Model;

use Leilao\Model\Lance;
use Leilao\Model\Leilao;
use Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    public function testLeilaoNaoDeveReceberLancesSeguidosDoMesmoUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é permitido efetuar 2 lances consecutivos.');

        $leilao = new Leilao('Gol 0KM');
        $antonio = new Usuario('Antonio');
        $leilao->recebeLance(new Lance($antonio, 7000));
        $leilao->recebeLance(new Lance($antonio, 8000));
    }

    /**
     * @dataProvider geraLances
     */
    public function testLeilaoDeveReceberLances(int $qtdLances, Leilao $leilao, array $lances)
    {
        static::assertCount($qtdLances, $leilao->getLances());

        foreach ($lances as $i => $lance) {
            static::assertEquals($lance, $leilao->getLances()[$i]->getValor());
        }
    }

    public function geraLances()
    {
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilaoCom2Lances = new Leilao('Fiat 147 0KM');
        $leilaoCom2Lances->recebeLance(new Lance($joao, 1000));
        $leilaoCom2Lances->recebeLance(new Lance($maria, 2000));

        $leilaoCom1Lance = new Leilao('Fusca 1972 0KM');
        $leilaoCom1Lance->recebeLance(new Lance($maria, 5000));

        return [
            '2-lances' => [2, $leilaoCom2Lances, [1000, 2000]],
            '1-lance' => [1, $leilaoCom1Lance, [5000]]
        ];
    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Você atingiu o número máximo de 5 lances.');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilao = new Leilao('Omega CD 0KM');
        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($joao, 2500));
        $leilao->recebeLance(new Lance($maria, 3000));
        $leilao->recebeLance(new Lance($joao, 3500));
        $leilao->recebeLance(new Lance($maria, 4000));
        $leilao->recebeLance(new Lance($joao, 4500));
        $leilao->recebeLance(new Lance($maria, 5000));
        $leilao->recebeLance(new Lance($joao, 5500));
        $leilao->recebeLance(new Lance($maria, 6000));

        $leilao->recebeLance(new Lance($joao, 6500));
    }

    public function testLeilaoFinalizadoNaoPodeReceberLances()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O leilão está finalizado e não pode receber mais lances.');

        $leilao = new Leilao('Corola 0KM');
        $leilao->finalizaLeilao();
        $leilao->recebeLance(new Lance(new Usuario('José'), 8000));
    }
}