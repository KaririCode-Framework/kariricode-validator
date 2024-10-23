# Framework KaririCode: Componente Validator

Um componente de validação de dados poderoso e flexível para PHP, parte do Framework KaririCode. Utiliza validação baseada em atributos com processadores configuráveis para garantir a integridade e validação dos dados em suas aplicações.

## Índice

- [Características](#características)
- [Instalação](#instalação)
- [Uso](#uso)
  - [Uso Básico](#uso-básico)
  - [Uso Avançado: Registro de Usuário](#uso-avançado-registro-de-usuário)
- [Validadores Disponíveis](#validadores-disponíveis)
  - [Validadores de Entrada](#validadores-de-entrada)
  - [Validadores Numéricos](#validadores-numéricos)
  - [Validadores Lógicos](#validadores-lógicos)
  - [Validadores de Data](#validadores-de-data)
- [Configuração](#configuração)
- [Integração com Outros Componentes KaririCode](#integração-com-outros-componentes-kariricode)
- [Desenvolvimento e Testes](#desenvolvimento-e-testes)
- [Contribuindo](#contribuindo)
- [Licença](#licença)
- [Suporte e Comunidade](#suporte-e-comunidade)

## Características

- Validação baseada em atributos para propriedades de objetos
- Conjunto abrangente de validadores integrados para casos de uso comuns
- Fácil integração com outros componentes KaririCode
- Processadores configuráveis para lógica de validação personalizada
- Suporte para mensagens de erro personalizadas
- Arquitetura extensível permitindo validadores personalizados
- Tratamento e relatório de erros robusto
- Pipelines de validação encadeáveis para validação complexa de dados
- Suporte integrado para múltiplos cenários de validação
- Validação segura de tipos com recursos do PHP 8.3

## Instalação

Você pode instalar o componente Validator via Composer:

```bash
composer require kariricode/validator
```

### Requisitos

- PHP 8.3 ou superior
- Composer
- Extensões: `ext-mbstring`, `ext-filter`

## Uso

### Uso Básico

1. Defina sua classe de dados com atributos de validação:

```php
use KaririCode\Validator\Attribute\Validate;

class PerfilUsuario
{
    #[Validate(
        processors: [
            'required',
            'length' => ['minLength' => 3, 'maxLength' => 20],
        ],
        messages: [
            'required' => 'Nome de usuário é obrigatório',
            'length' => 'Nome de usuário deve ter entre 3 e 20 caracteres',
        ]
    )]
    private string $username = '';

    #[Validate(
        processors: ['required', 'email'],
        messages: [
            'required' => 'Email é obrigatório',
            'email' => 'Formato de email inválido',
        ]
    )]
    private string $email = '';

    // Getters e setters...
}
```

2. Configure o validador e use-o:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Validator\Validator;
use KaririCode\Validator\Processor\Logic\RequiredValidator;
use KaririCode\Validator\Processor\Input\LengthValidator;
use KaririCode\Validator\Processor\Input\EmailValidator;

$registry = new ProcessorRegistry();
$registry->register('validator', 'required', new RequiredValidator());
$registry->register('validator', 'length', new LengthValidator());
$registry->register('validator', 'email', new EmailValidator());

$validator = new Validator($registry);

$perfilUsuario = new PerfilUsuario();
$perfilUsuario->setUsername('wa');  // Muito curto
$perfilUsuario->setEmail('email-invalido');  // Formato inválido

$resultado = $validator->validate($perfilUsuario);

if ($resultado->hasErrors()) {
    foreach ($resultado->getErrors() as $propriedade => $erros) {
        foreach ($erros as $erro) {
            echo "$propriedade: {$erro['message']}\n";
        }
    }
}
```

### Uso Avançado: Registro de Usuário

Aqui está um exemplo de como usar o Validator KaririCode em um cenário do mundo real, como validação de dados de registro de usuário:

```php
use KaririCode\Validator\Attribute\Validate;

class RegistroUsuario
{
    #[Validate(
        processors: [
            'required',
            'length' => ['minLength' => 3, 'maxLength' => 20],
        ],
        messages: [
            'required' => 'Nome de usuário é obrigatório',
            'length' => 'Nome de usuário deve ter entre 3 e 20 caracteres',
        ]
    )]
    private string $username = '';

    #[Validate(
        processors: ['required', 'email'],
        messages: [
            'required' => 'Email é obrigatório',
            'email' => 'Formato de email inválido',
        ]
    )]
    private string $email = '';

    #[Validate(
        processors: [
            'required',
            'length' => ['minLength' => 8],
        ],
        messages: [
            'required' => 'Senha é obrigatória',
            'length' => 'Senha deve ter pelo menos 8 caracteres',
        ]
    )]
    private string $password = '';

    #[Validate(
        processors: [
            'required',
            'integer',
            'range' => ['min' => 18, 'max' => 120],
        ],
        messages: [
            'required' => 'Idade é obrigatória',
            'integer' => 'Idade deve ser um número inteiro',
            'range' => 'Idade deve estar entre 18 e 120',
        ]
    )]
    private int $age = 0;

    // Getters e setters...
}

// Exemplo de uso
$registro = new RegistroUsuario();
$registro->setUsername('wm');  // Muito curto
$registro->setEmail('invalido');  // Formato inválido
$registro->setPassword('fraca');  // Muito curta
$registro->setAge(15);  // Muito jovem

$resultado = $validator->validate($registro);

// Processa resultados da validação
if ($resultado->hasErrors()) {
    $erros = $resultado->getErrors();
    // Trata erros de validação
} else {
    $dadosValidados = $resultado->getValidatedData();
    // Processa registro válido
}
```

## Validadores Disponíveis

### Validadores de Entrada

- **EmailValidator**: Valida endereços de email usando a função filter_var do PHP.

  - Chaves de Erro:
    - `invalidType`: Entrada não é uma string
    - `invalidFormat`: Formato de email inválido

- **LengthValidator**: Valida comprimento da string dentro dos limites especificados.

  - **Opções de Configuração**:
    - `minLength`: Comprimento mínimo permitido
    - `maxLength`: Comprimento máximo permitido
  - Chaves de Erro:
    - `invalidType`: Entrada não é uma string
    - `tooShort`: String é menor que minLength
    - `tooLong`: String é maior que maxLength

- **UrlValidator**: Valida URLs usando a função filter_var do PHP.
  - Chaves de Erro:
    - `invalidType`: Entrada não é uma string
    - `invalidFormat`: Formato de URL inválido

### Validadores Numéricos

- **IntegerValidator**: Garante que a entrada seja um inteiro válido.

  - Chaves de Erro:
    - `notAnInteger`: Entrada não é um inteiro válido

- **RangeValidator**: Valida valores numéricos dentro de um intervalo especificado.
  - **Opções de Configuração**:
    - `min`: Valor mínimo permitido
    - `max`: Valor máximo permitido
  - Chaves de Erro:
    - `notNumeric`: Entrada não é um número
    - `outOfRange`: Valor está fora do intervalo especificado

### Validadores Lógicos

- **RequiredValidator**: Garante que um valor não esteja vazio.
  - Chaves de Erro:
    - `missingValue`: Valor obrigatório está faltando ou vazio

### Validadores de Data

- **DateFormatValidator**: Valida datas contra um formato especificado.

  - **Opções de Configuração**:
    - `format`: String de formato de data (padrão: 'Y-m-d')
  - Chaves de Erro:
    - `invalidType`: Entrada não é uma string
    - `invalidFormat`: Data não corresponde ao formato especificado

- **DateRangeValidator**: Valida datas dentro de um intervalo especificado.
  - **Opções de Configuração**:
    - `minDate`: Data mínima permitida
    - `maxDate`: Data máxima permitida
    - `format`: String de formato de data (padrão: 'Y-m-d')
  - Chaves de Erro:
    - `invalidType`: Entrada não é uma string
    - `invalidDate`: Formato de data inválido
    - `outOfRange`: Data está fora do intervalo especificado

## Configuração

O componente Validator pode ser configurado globalmente ou por validador. Aqui está um exemplo de como configurar o `LengthValidator`:

```php
use KaririCode\Validator\Processor\Input\LengthValidator;

$lengthValidator = new LengthValidator();
$lengthValidator->configure([
    'minLength' => 3,
    'maxLength' => 20,
]);

$registry->register('validator', 'length', $lengthValidator);
```

## Integração com Outros Componentes KaririCode

O componente Validator foi projetado para trabalhar perfeitamente com outros componentes KaririCode:

- **KaririCode\Contract**: Fornece interfaces e contratos para integração consistente de componentes.
- **KaririCode\ProcessorPipeline**: Utilizado para construir e executar pipelines de validação.
- **KaririCode\PropertyInspector**: Usado para analisar e processar propriedades de objetos com atributos de validação.

## Explicação do Registry

O registry é um componente central para gerenciar validadores. Aqui está como configurar um registry completo:

```php
// Cria e configura o registry
$registry = new ProcessorRegistry();

// Registra todos os validadores necessários
$registry->register('validator', 'required', new RequiredValidator());
$registry->register('validator', 'email', new EmailValidator());
$registry->register('validator', 'length', new LengthValidator());
$registry->register('validator', 'integer', new IntegerValidator());
$registry->register('validator', 'range', new RangeValidator());
$registry->register('validator', 'url', new UrlValidator());
$registry->register('validator', 'dateFormat', new DateFormatValidator());
$registry->register('validator', 'dateRange', new DateRangeValidator());
```

## Desenvolvimento e Testes

Para fins de desenvolvimento e teste, este pacote usa Docker e Docker Compose para garantir consistência em diferentes ambientes. Um Makefile é fornecido para conveniência.

### Pré-requisitos

- Docker
- Docker Compose
- Make (opcional, mas recomendado para execução mais fácil de comandos)

### Configuração de Desenvolvimento

1. Clone o repositório:

   ```bash
   git clone https://github.com/KaririCode-Framework/kariricode-validator.git
   cd kariricode-validator
   ```

2. Configure o ambiente:

   ```bash
   make setup-env
   ```

3. Inicie os containers Docker:

   ```bash
   make up
   ```

4. Instale as dependências:

   ```bash
   make composer-install
   ```

### Comandos Make Disponíveis

- `make up`: Inicia todos os serviços em segundo plano
- `make down`: Para e remove todos os containers
- `make build`: Constrói imagens Docker
- `make shell`: Acessa o shell do container PHP
- `make test`: Executa testes
- `make coverage`: Executa cobertura de testes com formatação visual
- `make cs-fix`: Executa PHP CS Fixer para corrigir estilo de código
- `make quality`: Executa todos os comandos de qualidade (cs-check, test, security-check)

## Contribuindo

Nós recebemos contribuições para o componente KaririCode Validator! Aqui está como você pode contribuir:

1. Faça um fork do repositório
2. Crie um novo branch para sua feature ou correção de bug
3. Escreva testes para suas alterações
4. Implemente suas alterações
5. Execute a suite de testes e garanta que todos os testes passem
6. Envie um pull request com uma descrição clara de suas alterações

Por favor, leia nosso [Guia de Contribuição](CONTRIBUTING.md) para mais detalhes sobre nosso código de conduta e processo de desenvolvimento.

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte e Comunidade

- **Documentação**: [https://kariricode.org/docs/validator](https://kariricode.org/docs/validator)
- **Issue Tracker**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-validator/issues)
- **Fórum da Comunidade**: [Comunidade KaririCode Club](https://kariricode.club)
- **Stack Overflow**: Marque suas perguntas com `kariricode-validator`

---

Construído com ❤️ pela equipe KaririCode. Capacitando desenvolvedores para criar aplicações PHP mais seguras e robustas.
