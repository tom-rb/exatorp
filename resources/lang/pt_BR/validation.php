<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':Attribute deve ser aceito.',
    'active_url'           => ':Attribute não é uma URL válida.',
    'after'                => ':Attribute deve ser uma data depois de :date.',
    'alpha'                => ':Attribute deve conter somente letras.',
    'alpha_dash'           => ':Attribute deve conter letras, números e traços.',
    'alpha_num'            => ':Attribute deve conter somente letras e números.',
    'array'                => ':Attribute deve ser um array.',
    'before'               => ':Attribute deve ser uma data antes de :date.',
    'between'              => [
        'numeric' => ':Attribute deve estar entre :min e :max.',
        'file'    => ':Attribute deve estar entre :min e :max kilobytes.',
        'string'  => ':Attribute deve estar entre :min e :max caracteres.',
        'array'   => ':Attribute deve ter entre :min e :max itens.',
    ],
    'boolean'              => ':Attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'A confirmação de :attribute não confere.',
    'date'                 => ':Attribute não é uma data válida.',
    'date_format'          => ':Attribute não confere com o formato :format.',
    'different'            => ':Attribute e :other devem ser diferentes.',
    'digits'               => ':Attribute deve ter :digits dígitos apenas.',
    'digits_between'       => ':Attribute deve ter entre :min e :max dígitos.',
    'email'                => ':Attribute deve ser um endereço de e-mail válido.',
    'exists'               => 'O :attribute selecionado é inválido.',
    'filled'               => ':Attribute é um campo obrigatório.',
    'image'                => ':Attribute deve ser uma imagem.',
    'in'                   => ':Attribute é inválido.',
    'integer'              => ':Attribute deve ser um inteiro.',
    'ip'                   => ':Attribute deve ser um endereço IP válido.',
    'json'                 => ':Attribute deve ser um JSON válido.',
    'max'                  => [
        'numeric' => ':Attribute não deve ser maior que :max.',
        'file'    => ':Attribute não deve ter mais que :max kilobytes.',
        'string'  => ':Attribute não deve ter mais que :max caracteres.',
        'array'   => ':Attribute não pode ter mais que :max itens.',
    ],
    'mimes'                => ':Attribute deve ser um arquivo do tipo: :values.',
    'min'                  => [
        'numeric' => ':Attribute deve ser no mínimo :min.',
        'file'    => ':Attribute deve ter no mínimo :min kilobytes.',
        'string'  => ':Attribute deve ter no mínimo :min caracteres.',
        'array'   => ':Attribute deve ter no mínimo :min itens.',
    ],
    'not_in'               => 'O :attribute selecionado é inválido.',
    'numeric'              => ':Attribute deve ser um número.',
    'regex'                => 'O formato de :attribute é inválido.',
    'required'             => 'O campo :attribute é obrigatório.',
    'required_if'          => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_unless'      => 'O :attribute é necessário a menos que :other esteja em :values.',
    'required_with'        => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all'    => 'O campo :attribute é obrigatório quando :values estão presentes.',
    'required_without'     => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum destes estão presentes: :values.',
    'same'                 => ':Attribute e :other devem ser iguais.',
    'size'                 => [
        'numeric' => ':Attribute deve ser :size.',
        'file'    => ':Attribute deve ter :size kilobytes.',
        'string'  => ':Attribute deve ter :size caracteres.',
        'array'   => ':Attribute deve conter :size itens.',
    ],
    'string'               => ':Attribute deve ser uma string.',
    'timezone'             => ':Attribute deve ser uma timezone válida.',
    'unique'               => ':Attribute já está em uso.',
    'url'                  => 'O formato de :attribute é inválido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'alpha_spaces' => ':Attribute deve conter apenas letras e espaços.',
    'exists_where' => 'O :attribute selecionado é inválido.',
    'hash' => 'O campo :attribute informado não corresponde ao atual.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom message',
        ],
        'phones.*.required' => 'O :attribute não pode ficar em branco.',
        'first_area_job.*' => 'Selecione um cargo correspondente.',
        'second_area_job.*' => 'Selecione um cargo correspondente.',
        'availability.required' => 'Você precisa ter um horário disponível na semana!',
        'answers.*' => 'Não tem nada para contar? :( Expresse-se mais!',
        'how_did_you_hear.*' => 'Por favor, indique como ficou sabendo sobre o Exato.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email' => 'e-mail',
        'password' => 'senha',
        'current_password' => 'senha atual',
        'name' => 'nome',
        'phone' => 'telefone',
        'phones' => 'telefones',
        'phones.0' => 'primeiro telefone',
        'phones.1' => 'segundo telefone',
        'phones.2' => 'terceiro telefone',
        'phones.3' => 'quarto telefone',
        'ra' => 'RA',
        'course' => 'curso',
        'admission_year' => 'ano de ingresso',
        'first_area_id' => 'Primeira Opção',
        'first_area_job' => 'Cargo da 1ª Opção',
        'second_area_id' => 'Segunda Opção',
        'second_area_job' => 'Cargo da 2ª Opção',
        'availability' => 'Disponibilidade',
        'is_working' => 'está trabalhando',
        'how_did_you_hear' => 'como nos conheceu',
    ],

];
