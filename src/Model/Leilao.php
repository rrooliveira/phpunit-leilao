<?php

namespace Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    /** @var bool */
    private $status;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->status = true;
    }

    public function recebeLance(Lance $lance)
    {
        if (!$this->getStatusLeilao()) {
            throw new \DomainException('O leilão está finalizado e não pode receber mais lances.');
        }

        if (!empty($this->lances) && $this->verificarUsuarioUltimoLance($lance)) {
            throw new \DomainException('Não é permitido efetuar 2 lances consecutivos.');
        }

        $totalLancesPorUsuario = $this->quantidadeLancesPorUsuario($lance->getUsuario());

        if ($totalLancesPorUsuario >= 5) {
            throw new \DomainException('Você atingiu o número máximo de 5 lances.');
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    /**
     * @param Lance $lance
     * @return bool
     */
    private function verificarUsuarioUltimoLance(Lance $lance): bool
    {
        $usuario = $this->lances[array_key_last($this->getLances())]->getUsuario();

        return $lance->getUsuario() == $usuario;
    }

    /**
     * @param Usuario $usuario
     * @return mixed
     */
    private function quantidadeLancesPorUsuario(Usuario $usuario): int
    {
        $totalLancesPorUsuario = array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }
                return $totalAcumulado;
            },
            0
        );
        return $totalLancesPorUsuario;
    }

    public function finalizaLeilao() {
        $this->status = false;
    }

    public function getStatusLeilao(): bool
    {
        return $this->status;
    }


}