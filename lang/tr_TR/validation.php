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

    'accepted' => ':attribute alanı kabul edilmelidir.',
    'accepted_if' => ':attribute alanı, :other değeri :value olduğunda kabul edilmelidir.',
    'active_url' => ':attribute alanı geçerli bir URL olmalıdır.',
    'after' => ':attribute alanı :date değerinden sonraki bir tarih olmalıdır.',
    'after_or_equal' => ':attribute alanı :date tarihinden sonra veya ona eşit bir tarih olmalıdır.',
    'alpha' => ':attribute alanı yalnızca harf içermelidir.',
    'alpha_dash' => ':attribute alanı yalnızca harf, rakam, tire(-) ve alt çizgi(_) içermelidir.',
    'alpha_num' => ':attribute alanı yalnızca harf ve rakamlardan oluşmalıdır.',
    'array' => ':attribute alanı bir dizi olmalıdır.',
    'ascii' => ':attribute alanı yalnızca tek baytlık alfanümerik karakterler ve semboller içermelidir.',
    'before' => ':attribute alanı :date değerinden önceki bir tarih olmalıdır.',
    'before_or_equal' => ':attribute alanı :date tarihinden önce veya ona eşit bir tarih olmalıdır.',
    'between' => [
        'array' => ':attribute :min ile :max öğe arasında olmalıdır.',
        'file' => ':attribute :min ile :max kilobayt arasında olmalıdır.',
        'numeric' => ':attribute :min ile :max arasında olmalıdır.',
        'string' => ':attribute :min ile :max karakter arasında olmalıdır.',
    ],
    'boolean' => ':attribute alanı doğru veya yanlış olmalıdır.',
    'can' => ':attribute alanı yetkisiz bir değer içeriyor.',
    'confirmed' => ':attribute doğrulaması eşleşmiyor.',
    'current_password' => 'Şifre yanlış.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => ':attribute :date tarihine eşit bir tarih olmalıdır.',
    'date_format' => ':attribute :format formatıyla eşleşmelidir.',
    'decimal' => ':attribute :decimal ondalık basamak içermelidir.',
    'declined' => ':attribute reddedilmelidir.',
    'declined_if' => ':attribute, :other :value olduğunda reddedilmelidir.',
    'different' => ':attribute ve :other farklı olmalıdır.',
    'digits' => ':attribute :digits basamaklı olmalıdır.',
    'digits_between' => ':attribute :min ile :max basamak arasında olmalıdır.',
    'dimensions' => ':attribute geçersiz resim boyutlarına sahiptir.',
    'distinct' => ':attribute alanında yinelenen bir değer var.',
    'doesnt_end_with' => ':attribute şu değerlerden biriyle bitmemelidir: :values.',
    'doesnt_start_with' => ':attribute şu değerlerden biriyle başlamamalıdır: :values.',
    'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute şu değerlerden biriyle bitmelidir: :values.',
    'enum' => 'Seçilen :attribute geçersiz.',
    'exists' => 'Seçilen :attribute geçersiz.',
    'file' => ':attribute bir dosya olmalıdır.',
    'filled' => ':attribute bir değer içermelidir.',
    'gt' => [
        'array' => ':attribute :value öğeden fazla olmalıdır.',
        'file' => ':attribute :value kilobayttan büyük olmalıdır.',
        'numeric' => ':attribute :value değerinden büyük olmalıdır.',
        'string' => ':attribute :value karakterden uzun olmalıdır.',
    ],
    'gte' => [
        'array' => ':attribute :value veya daha fazla öğe içermelidir.',
        'file' => ':attribute :value kilobayt veya daha büyük olmalıdır.',
        'numeric' => ':attribute :value değerine eşit veya daha büyük olmalıdır.',
        'string' => ':attribute :value karakter veya daha fazla olmalıdır.',
    ],
    'image' => ':attribute bir resim olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'in_array' => ':attribute, :other içinde mevcut olmalıdır.',
    'integer' => ':attribute bir tam sayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON metni olmalıdır.',
    'lowercase' => ':attribute küçük harflerden oluşmalıdır.',
    'lt' => [
        'array' => ':attribute :value öğeden az olmalıdır.',
        'file' => ':attribute :value kilobayttan küçük olmalıdır.',
        'numeric' => ':attribute :value değerinden küçük olmalıdır.',
        'string' => ':attribute :value karakterden kısa olmalıdır.',
    ],
    'lte' => [
        'array' => ':attribute :value öğeden fazla olmamalıdır.',
        'file' => ':attribute :value kilobayt veya daha az olmalıdır.',
        'numeric' => ':attribute :value değerine eşit veya daha küçük olmalıdır.',
        'string' => ':attribute :value karakter veya daha az olmalıdır.',
    ],
    'mac_address' => ':attribute geçerli bir MAC adresi olmalıdır.',
    'max' => [
        'array' => ':attribute :max öğeden fazla olmamalıdır.',
        'file' => ':attribute :max kilobayttan büyük olmamalıdır.',
        'numeric' => ':attribute :max değerinden büyük olmamalıdır.',
        'string' => ':attribute :max karakterden uzun olmamalıdır.',
    ],
    'max_digits' => ':attribute :max basamaktan fazla olmamalıdır.',
    'mimes' => ':attribute şu türde bir dosya olmalıdır: :values.',
    'mimetypes' => ':attribute şu türde bir dosya olmalıdır: :values.',
    'min' => [
        'array' => ':attribute en az :min öğe içermelidir.',
        'file' => ':attribute en az :min kilobayt olmalıdır.',
        'numeric' => ':attribute en az :min olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
    ],
    'min_digits' => ':attribute en az :min basamak içermelidir.',
    'missing' => ':attribute eksik olmalıdır.',
    'missing_if' => ':attribute, :other :value olduğunda eksik olmalıdır.',
    'missing_unless' => ':attribute, :other :value değilse eksik olmalıdır.',
    'missing_with' => ':attribute, :values mevcut olduğunda eksik olmalıdır.',
    'missing_with_all' => ':attribute, :values mevcut olduğunda eksik olmalıdır.',
    'multiple_of' => ':attribute :value katı olmalıdır.',
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':attribute formatı geçersiz.',
    'numeric' => ':attribute bir sayı olmalıdır.',
    'password' => [
        'letters' => ':attribute en az bir harf içermelidir.',
        'mixed' => ':attribute en az bir büyük harf ve bir küçük harf içermelidir.',
        'numbers' => ':attribute en az bir rakam içermelidir.',
        'symbols' => ':attribute en az bir sembol içermelidir.',
        'uncompromised' => 'Verilen :attribute bir veri ihlalinde tespit edilmiştir. Lütfen farklı bir :attribute seçin.',
    ],
    'present' => ':attribute mevcut olmalıdır.',
    'prohibited' => ':attribute yasaktır.',
    'prohibited_if' => ':attribute, :other :value olduğunda yasaktır.',
    'prohibited_unless' => ':attribute, :other :values içinde olmadıkça yasaktır.',
    'prohibits' => ':attribute, :other alanının mevcut olmasını yasaklar.',
    'regex' => ':attribute formatı geçersiz.',
    'required' => ':attribute alanı gereklidir.',
    'required_array_keys' => ':attribute şu anahtarları içermelidir: :values.',
    'required_if' => ':attribute, :other :value olduğunda gereklidir.',
    'required_if_accepted' => ':attribute, :other kabul edildiğinde gereklidir.',
    'required_unless' => ':attribute, :other :values içinde olmadıkça gereklidir.',
    'required_with' => ':attribute, :values mevcut olduğunda gereklidir.',
    'required_with_all' => ':attribute, :values mevcut olduğunda gereklidir.',
    'required_without' => ':attribute, :values mevcut değilse gereklidir.',
    'required_without_all' => ':attribute, :values hiçbirisi mevcut değilse gereklidir.',
    'same' => ':attribute, :other ile eşleşmelidir.',
    'size' => [
        'array' => ':attribute :size öğe içermelidir.',
        'file' => ':attribute :size kilobayt olmalıdır.',
        'numeric' => ':attribute :size olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
    ],
    'starts_with' => ':attribute şu değerlerden biriyle başlamalıdır: :values.',
    'string' => ':attribute bir metin olmalıdır.',
    'timezone' => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique' => ':attribute zaten alınmış.',
    'uploaded' => ':attribute yüklenemedi.',
    'uppercase' => ':attribute büyük harflerden oluşmalıdır.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'ulid' => ':attribute geçerli bir ULID olmalıdır.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',

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
        'address' => 'adres',
        'age' => 'yaş',
        'body' => 'içerik',
        'cell' => 'hücre',
        'city' => 'şehir',
        'country' => 'ülke',
        'date' => 'tarih',
        'day' => 'gün',
        'excerpt' => 'özet',
        'first_name' => 'ad',
        'gender' => 'cinsiyet',
        'marital_status' => 'medeni hal',
        'profession' => 'meslek',
        'nationality' => 'uyruk',
        'hour' => 'saat',
        'last_name' => 'soyad',
        'message' => 'mesaj',
        'minute' => 'dakika',
        'mobile' => 'cep telefonu',
        'month' => 'ay',
        'name' => 'isim',
        'zipcode' => 'posta kodu',
        'company_name' => 'şirket adı',
        'neighborhood' => 'mahalle',
        'number' => 'numara',
        'password' => 'şifre',
        'phone' => 'telefon',
        'second' => 'saniye',
        'sex' => 'cinsiyet',
        'state' => 'eyalet',
        'street' => 'sokak',
        'subject' => 'konu',
        'text' => 'metin',
        'time' => 'zaman',
        'title' => 'başlık',
        'username' => 'kullanıcı adı',
        'year' => 'yıl',
        'description' => 'açıklama',
        'password_confirmation' => 'şifre doğrulama',
        'current_password' => 'mevcut şifre',
        'complement' => 'ek bilgi',
        'modality' => 'mod',
        'category' => 'kategori',
        'blood_type' => 'kan grubu',
        'birth_date' => 'doğum tarihi',
    ],
];
