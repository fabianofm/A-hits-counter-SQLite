<?php
/**
 * Classe contador em PHP5 utilizando o SQLite.
 *
 * @author Fabiano Monteiro
 * @version 1.0 2010-06-11
 * @license LGPL3
 * @copyright (C) 2010 Fabiano Monteiro
 */

/**
*Algumas dicas:
*
*- Não coloque os arquivos do banco em diretórios visíveis na web - ou seja, evitem *colocá-los abaixo da pasta public_html.
*Se for necessário colocar dentro desta estrutura, proteja o diretorio com .htaccess;
*
*- Utilize tabelas com poucos dados. O SQLite roda no mesmo servidor que o seu site (ao *contrário do MySQL, PostgreSQL que rodam em servidores separados) e pode comprometer a *performance se ele for muito grande ou tiver muitos acessos;
*
*- Para o nome do arquivos, utilize sufixos como ".db", ".sqlite" para lembrá-lo que o *arquivo é um banco de dados.
*
**/

class Contador
{
    protected $banco;
    public $resValor;

    public function  __construct()
    {
        try {
            // Se não existir, cria a base de dados.
            // Cria a instância.
            $this->banco = new SQLite3('contadordb.db');

        } catch (Exception $exc) {
            die( $exc->getMessage() );
        }
       
         // Se a tabela não foi populada, o método 'exibirValor()' retorna FALSE.
        // Leia o comentário do método 'exibirValor()'
        if(!self::exibirValor()){
            // Cria a tabela
            $this->banco->exec('CREATE TABLE contador (valor INTEGER )');
            $this->banco->exec('INSERT INTO contador (valor) VALUES (0)');
        }
    }


    public function exibirValor()
    {
        // 'querySingle' - Executa a query e retorna um único resultado.
        // Por padrão retorna o valor da primeira coluna em forma de array(matriz),
        // caso contrário, o valor de retorno é FALSE.
        $this->resValor = $this->banco->querySingle('SELECT valor FROM contador');

        return $this->resValor;
    }
   
    public function atualizarContador()
    {
        // Pega o valor da consulta única ($this->resValor).
        self::exibirValor();

        // Somando e atualizando a base.
        $valor = $this->resValor + 1;
        $this->banco->exec("UPDATE contador SET valor = $valor");
    }

    public function  __destruct()
    {
        $this->banco->close();
    }
}


$meuContador = new Contador();

// O Método pode entrar em alguma condição para tratar
// o acesso por visita e atualizar uma única vez gravando em sessão.
$meuContador->atualizarContador();

//Método para exibir o valor do contador.
echo 'Contador '. $meuContador->exibirValor();


?>
