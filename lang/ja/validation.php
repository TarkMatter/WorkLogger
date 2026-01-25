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

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeには英字のみ使用できます。',
    'alpha_dash' => ':attributeには英数字、ハイフン(-)、アンダースコア(_)のみ使用できます。',
    'alpha_num' => ':attributeには英数字のみ使用できます。',
    'any_of' => ':attributeが正しくありません。',
    'array' => ':attributeには配列を指定してください。',
    'ascii' => ':attributeには半角英数字と記号のみ使用できます。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',

    'between' => [
        'array' => ':attributeは:min〜:max個の項目にしてください。',
        'file' => ':attributeは:min〜:maxKBのファイルにしてください。',
        'numeric' => ':attributeは:min〜:maxの間で指定してください。',
        'string' => ':attributeは:min〜:max文字で入力してください。',
    ],

    'boolean' => ':attributeにはtrueまたはfalseを指定してください。',
    'can' => ':attributeに許可されていない値が含まれています。',
    'confirmed' => ':attributeと確認用が一致しません。',
    'contains' => ':attributeに必要な値が含まれていません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeは有効な日付ではありません。',
    'date_equals' => ':attributeには:dateと同じ日付を指定してください。',
    'date_format' => ':attributeの形式は:formatと一致しません。',
    'decimal' => ':attributeは小数点以下:decimal桁で指定してください。',
    'declined' => ':attributeは拒否する必要があります。',
    'declined_if' => ':otherが:valueの場合、:attributeは拒否する必要があります。',
    'different' => ':attributeと:otherは異なる値を指定してください。',
    'digits' => ':attributeは:digits桁で入力してください。',
    'digits_between' => ':attributeは:min〜:max桁で入力してください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_contain' => ':attributeには次の値を含めないでください: :values。',
    'doesnt_end_with' => ':attributeの末尾は次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attributeの先頭は次のいずれかで始まってはいけません: :values。',
    'email' => ':attributeは有効なメールアドレス形式で入力してください。',
    'encoding' => ':attributeは:encodingでエンコードされている必要があります。',
    'ends_with' => ':attributeの末尾は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された:attributeが正しくありません。',
    'exists' => '選択された:attributeが正しくありません。',
    'extensions' => ':attributeの拡張子は次のいずれかである必要があります: :values。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeは必須です。',

    'gt' => [
        'array' => ':attributeは:value個より多くの項目にしてください。',
        'file' => ':attributeは:valueKBより大きいファイルにしてください。',
        'numeric' => ':attributeは:valueより大きい値を指定してください。',
        'string' => ':attributeは:value文字より多く入力してください。',
    ],

    'gte' => [
        'array' => ':attributeは:value個以上の項目にしてください。',
        'file' => ':attributeは:valueKB以上のファイルにしてください。',
        'numeric' => ':attributeは:value以上の値を指定してください。',
        'string' => ':attributeは:value文字以上で入力してください。',
    ],

    'hex_color' => ':attributeは有効な16進カラーコードではありません。',
    'image' => ':attributeには画像ファイルを指定してください。',
    'in' => '選択された:attributeが正しくありません。',
    'in_array' => ':attributeは:otherに存在しません。',
    'in_array_keys' => ':attributeには次のキーのいずれかを含めてください: :values。',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'list' => ':attributeにはリストを指定してください。',
    'lowercase' => ':attributeは小文字で入力してください。',

    'lt' => [
        'array' => ':attributeは:value個より少ない項目にしてください。',
        'file' => ':attributeは:valueKBより小さいファイルにしてください。',
        'numeric' => ':attributeは:valueより小さい値を指定してください。',
        'string' => ':attributeは:value文字より少なく入力してください。',
    ],

    'lte' => [
        'array' => ':attributeは:value個以下の項目にしてください。',
        'file' => ':attributeは:valueKB以下のファイルにしてください。',
        'numeric' => ':attributeは:value以下の値を指定してください。',
        'string' => ':attributeは:value文字以下で入力してください。',
    ],

    'mac_address' => ':attributeは有効なMACアドレスではありません。',

    'max' => [
        'array' => ':attributeは:max個以下の項目にしてください。',
        'file' => ':attributeは:maxKB以下のファイルにしてください。',
        'numeric' => ':attributeは:max以下の値を指定してください。',
        'string' => ':attributeは:max文字以内で入力してください。',
    ],

    'max_digits' => ':attributeは:max桁以下で入力してください。',
    'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',

    'min' => [
        'array' => ':attributeは:min個以上の項目にしてください。',
        'file' => ':attributeは:minKB以上のファイルにしてください。',
        'numeric' => ':attributeは:min以上の値を指定してください。',
        'string' => ':attributeは:min文字以上で入力してください。',
    ],

    'min_digits' => ':attributeは:min桁以上で入力してください。',
    'missing' => ':attributeは指定しないでください。',
    'missing_if' => ':otherが:valueの場合、:attributeは指定しないでください。',
    'missing_unless' => ':otherが:valueでない場合、:attributeは指定しないでください。',
    'missing_with' => ':valuesが存在する場合、:attributeは指定しないでください。',
    'missing_with_all' => ':valuesが存在する場合、:attributeは指定しないでください。',
    'multiple_of' => ':attributeは:valueの倍数を指定してください。',
    'not_in' => '選択された:attributeが正しくありません。',
    'not_regex' => ':attributeの形式が正しくありません。',
    'numeric' => ':attributeには数値を指定してください。',

    'password' => [
        'letters' => ':attributeには少なくとも1つの英字を含めてください。',
        'mixed' => ':attributeには大文字と小文字をそれぞれ少なくとも1つ含めてください。',
        'numbers' => ':attributeには少なくとも1つの数字を含めてください。',
        'symbols' => ':attributeには少なくとも1つの記号を含めてください。',
        'uncompromised' => '指定された:attributeは漏洩したパスワードに含まれています。別の:attributeを指定してください。',
    ],

    'present' => ':attributeは必ず指定してください。',
    'present_if' => ':otherが:valueの場合、:attributeは必ず指定してください。',
    'present_unless' => ':otherが:valueでない場合、:attributeは必ず指定してください。',
    'present_with' => ':valuesが存在する場合、:attributeは必ず指定してください。',
    'present_with_all' => ':valuesが存在する場合、:attributeは必ず指定してください。',
    'prohibited' => ':attributeは指定できません。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは指定できません。',
    'prohibited_if_accepted' => ':otherが承認されている場合、:attributeは指定できません。',
    'prohibited_if_declined' => ':otherが拒否されている場合、:attributeは指定できません。',
    'prohibited_unless' => ':otherが:valuesに含まれない場合、:attributeは指定できません。',
    'prohibits' => ':attributeが指定されている場合、:otherは指定できません。',
    'regex' => ':attributeの形式が正しくありません。',
    'required' => ':attributeは必須です。',
    'required_array_keys' => ':attributeには次の項目を含めてください: :values。',
    'required_if' => ':otherが:valueの場合、:attributeは必須です。',
    'required_if_accepted' => ':otherが承認されている場合、:attributeは必須です。',
    'required_if_declined' => ':otherが拒否されている場合、:attributeは必須です。',
    'required_unless' => ':otherが:valuesに含まれない場合、:attributeは必須です。',
    'required_with' => ':valuesが存在する場合、:attributeは必須です。',
    'required_with_all' => ':valuesが存在する場合、:attributeは必須です。',
    'required_without' => ':valuesが存在しない場合、:attributeは必須です。',
    'required_without_all' => ':valuesがすべて存在しない場合、:attributeは必須です。',
    'same' => ':attributeと:otherは同じ値を指定してください。',

    'size' => [
        'array' => ':attributeは:size個の項目にしてください。',
        'file' => ':attributeは:sizeKBのファイルにしてください。',
        'numeric' => ':attributeは:sizeを指定してください。',
        'string' => ':attributeは:size文字で入力してください。',
    ],

    'starts_with' => ':attributeの先頭は次のいずれかで始まる必要があります: :values。',
    'string' => ':attributeには文字列を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => ':attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字で入力してください。',
    'url' => ':attributeは有効なURLではありません。',
    'ulid' => ':attributeは有効なULIDではありません。',
    'uuid' => ':attributeは有効なUUIDではありません。',

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

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
        'role' => '権限',
        'report_date' => '日付',
        'memo' => 'メモ',
        'rejection_reason' => '差戻し理由',
        'project_id' => '案件',
        'projects' => '案件',
        'description' => '説明',
        'task' => '作業内容',
        'minutes' => '工数（分）',
        'permissions' => '権限',
    ],

];
