@extends('layouts.base')
@section('title', trans('auth.registermember'))

@section('main-content')
    <section class="section">
        <div class="container">
            <div class="heading">
                <h1 class="title">Inscrição para o voluntariado</h1>
            </div>
            <p class="subtitle">Cadastre-se no processo de seleção de novos voluntários e voluntárias para o Curso Exato!</p>

            <div class="content">
                <h2 class="subtitle is-5">Prezado(a) candidato(a),</h2>
                <p>
                    Antes de se inscrever em nosso processo seletivo, é importante que você conheça um pouco mais da nossa
                    dinâmica e do que será esperado de você, para que você tenha certeza se o Exato atenderá às suas
                    expectativas e se você poderá corresponder às nossas.
                </p>

                <h2 class="subtitle is-5">O que esperar do Exato?</h2>
                <p>
                    O Exato é um projeto que conta com o apoio da Pró-Reitoria de Extensão e Assuntos Comunitários (PREAC),
                    e tem como objetivo contribuir com o desenvolvimento de alunos da rede pública. Nós não somos um cursinho
                    (não oferecemos aulas de todas as disciplinas nem temos como pretensão cumprir todo o currículo do Ensino
                    Médio e não impomos um ritmo motivado pela data dos vestibulares): em vez disso, <strong>focamos em
                    reforçar a base</strong> dos nossos estudantes, de modo a ajudá-los a conseguirem o que quer que escolham
                    fazer após se formarem. Desse modo, muitas vezes a nossa atuação se limita a tópicos selecionados dentre
                    os normalmente trabalhados no colegial.
                </p>
                <p>
                    As aulas acontecem no período noturno, nas salas do Prédio Básico (PB), <strong>de segunda a
                    quinta-feira</strong> (com algumas atividades esporádicas às sextas-feiras), em dois blocos: <strong>das
                    19h15 às 20h45 e das 21h às 22h30.</strong> O primeiro bloco conta com a presença de um professor, que
                    ministra aulas teóricas sobre um tópico escolhido, e o segundo bloco conta com atividades práticas, com
                    a presença de monitores e supervisionado por outro professor. Normalmente as atividades práticas envolvem
                    a divisão da turma em grupos para resolverem listas de exercícios sobre o assunto da respectiva aula teórica.
                    O professor controla o ritmo da aula, separa exercícios da lista para guiar o trabalho dos alunos e os
                    monitores se revezam entre os alunos/grupos para tirar dúvidas e ajudá-los a construir as soluções. Cada
                    dia da semana é dedicado a uma disciplina diferente dentre <strong>química, física, matemática e português.</strong>
                </p>

                <h2 class="subtitle is-5">Além das suas funções principais, o que esperamos de você?</h2>
                <p>
                    A comunicação interna é um fator essencial para o bom funcionamento do Curso Exato. Por isso, temos
                    <strong>reuniões presenciais semanalmente</strong> que contam com a presença de toda a equipe, inclusive dos nossos
                    orientadores. Também utilizamos um fórum online como ferramenta oficial para discussão de todo e qualquer
                    assunto relativo ao curso. Tanto o Fórum quanto o Google Drive, onde armazenamos documentos, são
                    utilizados para, além da comunicação da equipe atual, documentação para as equipes futuras.
                </p>
                <p>
                    De todos os membros da equipe didática (professores e monitores), espera-se que participem das reuniões
                    setoriais e gerais (que acontecem quinzenalmente em semanas intercaladas), além de terem participação
                    ativa no fórum e nas discussões propostas.
                </p>

                <h2 class="subtitle is-5">E quais serão suas funções principais?</h2>
                <ul>
                    <li class="is-spaced-bottom-1">
                        <p>Professores</p>
                        <p>Além de ministrarem as aulas, dediquem-se a planejá-las (de modo a cumprirem as atividades
                            propostas no tempo determinado), a selecionar os exercícios que serão usadas nas aulas práticas
                            em apostilas prontas, e a fornecer feedback sobre as experiências de sala de aula.</p>
                    </li>
                    <li class="is-spaced-bottom-1">
                        <p>Monitores</p>
                        <p>Além de estarem presentes nas aulas práticas, colaborem com a confecção das listas de exercício e
                            forneçam feedback aos professores sobre o material adotado, os alunos e suas dificuldades.</p>
                    </li>
                    <li class="is-spaced-bottom-1">
                        <p>Redação de material</p>
                        <p>Participem da reunião exclusiva de tal equipe - a necessidade e a relevância da participação desta
                            equipe nas reuniões setoriais e gerais anteriormente citadas vai ser definida ao longo do semestre,
                            além de terem participação ativa no fórum e nas discussões propostas. Dos redatores, espera-se
                            semanalmente a confecção de exercícios autorais para a apostila. Estes exercícios não podem ser
                            plagiados de livros, apostilas, vestibulares, etc. Os redatores deverão ter o apoio da equipe
                            didática como uma forma de guia para melhor compreensão das necessidades da sala de aula.</p>
                    </li>
                    <li class="is-spaced-bottom-1">
                        <p>Edição de material</p>
                        <p>Aos editores, caberá a tarefa de adaptar e unificar a linguagem utilizada nos exercícios fornecidos
                            pelos redatores, assim como alterações na sequência e, eventualmente, exclusão de exercícios de
                            acordo com o ideal do material.</p>
                    </li>
                    <li class="is-spaced-bottom-1">
                        <p>Ilustração de material</p>
                        <p>Dos ilustradores, espera-se a confecção das ilustrações requisitadas pelos redatores. Também
                            serão responsáveis por buscar imagens prontas (por exemplo, fotos históricas) em bancos de
                            imagens gratuitos. Os editoradores serão responsáveis pela etapa final, unindo em um único
                            arquivo textos, imagem e design para a impressão do material didático.</p>
                    </li>
                </ul>
            </div>

            <article class="message is-primary">
                <div class="message-header">
                    <p>Disciplina AM</p>
                </div>
                <div class="message-body">
                    O processo seletivo <strong>terminará depois do período de alteração de matrículas</strong>.
                    Os matriculados na disciplina AM vinculada ao Curso Exato que não desejam trancá-la (caso não sejam
                    convocados) devem retirar a disciplina durante o período de alteração de matrícula.
                </div>
            </article>

            {!! Form::open(['method' => 'get']) !!}

            <div class="field">
                <b-checkbox name="agreed" :native-value="true">
                    Li e concordo com o <a @click.prevent="showModal = true">Termo de adesão para Trabalho Voluntário</a>.
                </b-checkbox>
            </div>

            @include('form.button', ['label' => 'Entendi', 'icon' => 'check',
                                     'class' => 'is-primary is-spaced-1'])

            {!! Form::close() !!}
        </div>
    </section>

    <b-modal :active.sync="showModal" has-modal-card>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Termo de adesão</p>
            </header>
            <section class="modal-card-body">
                @include('selection-process._agreement')
            </section>
            <footer class="modal-card-foot">
                <button class="button is-success" @click="showModal = false">Fechar</button>
            </footer>
        </div>
    </b-modal>
@endsection