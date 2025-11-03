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

    'accepted' => 'A(z) :attribute el kell legyen fogadva.',
    'accepted_if' => 'A(z) :attribute el kell legyen fogadva, ha :other értéke :value.',
    'active_url' => 'A(z) :attribute nem érvényes URL.',
    'after' => 'A(z) :attribute dátumának későbbinek kell lennie, mint :date.',
    'after_or_equal' => 'A(z) :attribute dátumának legalább :date-nek kell lennie.',
    'alpha' => 'A(z) :attribute csak betűket tartalmazhat.',
    'alpha_dash' => 'A(z) :attribute csak betűket, számokat, kötőjeleket és aláhúzásjeleket tartalmazhat.',
    'alpha_num' => 'A(z) :attribute csak betűket és számokat tartalmazhat.',
    'array' => 'A(z) :attribute egy tömbnek kell lennie.',
    'ascii' => 'A(z) :attribute csak ASCII karaktereket tartalmazhat.',
    'before' => 'A(z) :attribute dátumának korábbinak kell lennie, mint :date.',
    'before_or_equal' => 'A(z) :attribute dátumának legfeljebb :date-nek kell lennie.',
    'between' => [
        'array' => 'A(z) :attribute :min és :max elem között kell legyen.',
        'file' => 'A(z) :attribute mérete :min és :max kilobájt között kell legyen.',
        'numeric' => 'A(z) :attribute értékének :min és :max között kell lennie.',
        'string' => 'A(z) :attribute :min és :max karakter között kell legyen.',
    ],
    'boolean' => 'A(z) :attribute értéke igaz vagy hamis lehet.',
    'can' => 'A(z) :attribute érvénytelen értéket tartalmaz.',
    'confirmed' => 'A(z) :attribute megerősítése nem egyezik.',
    'current_password' => 'A megadott jelszó helytelen.',
    'date' => 'A(z) :attribute nem érvényes dátum.',
    'date_equals' => 'A(z) :attribute pontosan :date kell legyen.',
    'date_format' => 'A(z) :attribute nem felel meg a formátumnak: :format.',
    'decimal' => 'A(z) :attribute :decimal tizedesjegyet kell tartalmazzon.',
    'declined' => 'A(z) :attribute el kell legyen utasítva.',
    'declined_if' => 'A(z) :attribute el kell legyen utasítva, ha :other értéke ":value".',
    'different' => 'A(z) :attribute és :other nem lehet azonos.',
    'digits' => 'A(z) :attribute :digits számjegyből kell álljon.',
    'digits_between' => 'A(z) :attribute :min és :max számjegy közötti érték kell legyen.',
    'dimensions' => 'A(z) :attribute érvénytelen képméretet tartalmaz.',
    'distinct' => 'A(z) :attribute mező ismétlődő értéket tartalmaz.',
    'doesnt_end_with' => 'A(z) :attribute nem végződhet a következőkkel: :values.',
    'doesnt_start_with' => 'A(z) :attribute nem kezdődhet a következőkkel: :values.',
    'email' => 'A(z) :attribute érvényes e-mail cím kell legyen.',
    'ends_with' => 'A(z) :attribute a következő értékek egyikével kell végződjön: :values.',
    'enum' => 'A kiválasztott :attribute érvénytelen.',
    'exists' => 'A kiválasztott :attribute már létezik.',
    'file' => 'A(z) :attribute fájlnak kell lennie.',
    'filled' => 'A(z) :attribute mező nem lehet üres.',
    'gt' => [
        'array' => 'A(z) :attribute több mint :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete nagyobb kell legyen, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute nagyobb kell legyen, mint :value.',
        'string' => 'A(z) :attribute hosszabb kell legyen, mint :value karakter.',
    ],
    'gte' => [
        'array' => 'A(z) :attribute legalább :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete legalább :value kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute legalább :value kell legyen.',
        'string' => 'A(z) :attribute legalább :value karakter hosszú kell legyen.',
    ],
    'image' => 'A(z) :attribute képnek kell lennie.',
    'in' => 'A(z) :attribute értéke érvénytelen.',
    'in_array' => 'A(z) :attribute nem található meg a(z) :other mezőben.',
    'integer' => 'A(z) :attribute egész szám kell legyen.',
    'ip' => 'A(z) :attribute érvényes IP-cím kell legyen.',
    'ipv4' => 'A(z) :attribute érvényes IPv4-cím kell legyen.',
    'ipv6' => 'A(z) :attribute érvényes IPv6-cím kell legyen.',
    'json' => 'A(z) :attribute érvényes JSON kell legyen.',
    'lowercase' => 'A(z) :attribute csak kisbetűket tartalmazhat.',
    'lt' => [
        'array' => 'A(z) :attribute legfeljebb :value elemet tartalmazhat.',
        'file' => 'A(z) :attribute kisebb kell legyen, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute kisebb kell legyen, mint :value.',
        'string' => 'A(z) :attribute rövidebb kell legyen, mint :value karakter.',
    ],
    'lte' => [
        'array' => 'A(z) :attribute nem tartalmazhat több, mint :value elemet.',
        'file' => 'A(z) :attribute nem lehet nagyobb, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute nem lehet nagyobb, mint :value.',
        'string' => 'A(z) :attribute nem lehet hosszabb, mint :value karakter.',
    ],
    'mac_address' => 'A(z) :attribute érvényes MAC-cím kell legyen.',
    'max' => [
        'array' => 'A(z) :attribute legfeljebb :max elemet tartalmazhat.',
        'file' => 'A(z) :attribute legfeljebb :max kilobájt lehet.',
        'numeric' => 'A(z) :attribute nem lehet nagyobb, mint :max.',
        'string' => 'A(z) :attribute nem lehet hosszabb, mint :max karakter.',
    ],
    'max_digits' => 'A(z) :attribute legfeljebb :max számjegyet tartalmazhat.',
    'mimes' => 'A(z) :attribute típusának a következők egyikének kell lennie: :values.',
    'mimetypes' => 'A(z) :attribute formátuma a következők egyike kell legyen: :values.',
    'min' => [
        'array' => 'A(z) :attribute legalább :min elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute legalább :min kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute legalább :min kell legyen.',
        'string' => 'A(z) :attribute legalább :min karakter hosszú kell legyen.',
    ],
    'min_digits' => 'A(z) :attribute legalább :min számjegyet kell tartalmazzon.',
    'missing' => 'A(z) :attribute nem szerepelhet.',
    'missing_if' => 'A(z) :attribute nem lehet megadva, ha :other értéke ":value".',
    'missing_unless' => 'A(z) :attribute nem lehet megadva, kivéve ha :other értéke ":value".',
    'missing_with' => 'A(z) :attribute nem szerepelhet, ha :values meg van adva.',
    'missing_with_all' => 'A(z) :attribute nem szerepelhet, ha a(z) :values mezők mind meg vannak adva.',
    'multiple_of' => 'A(z) :attribute a(z) :value többszöröse kell legyen.',
    'not_in' => 'A kiválasztott :attribute érvénytelen.',
    'not_regex' => 'A(z) :attribute formátuma érvénytelen.',
    'numeric' => 'A(z) :attribute szám kell legyen.',
    'password' => [
        'letters' => 'A(z) :attribute tartalmazzon legalább egy betűt.',
        'mixed' => 'A(z) :attribute tartalmazzon legalább egy kis- és egy nagybetűt.',
        'numbers' => 'A(z) :attribute tartalmazzon legalább egy számot.',
        'symbols' => 'A(z) :attribute tartalmazzon legalább egy speciális karaktert.',
        'uncompromised' => 'A(z) :attribute egy adatszivárgásban érintett. Kérjük, válasszon másik :attribute-t.',
    ],
    'present' => 'A(z) :attribute mezőnek jelen kell lennie.',
    'prohibited' => 'A(z) :attribute megadása nem engedélyezett.',
    'prohibited_if' => 'A(z) :attribute nem adható meg, ha :other értéke ":value".',
    'prohibited_unless' => 'A(z) :attribute csak akkor adható meg, ha :other értéke ":values".',
    'prohibits' => 'A(z) :attribute kizárja a(z) :other megadását.',
    'regex' => 'A(z) :attribute formátuma érvénytelen.',
    'required' => 'A(z) :attribute mező kötelező.',
    'required_array_keys' => 'A(z) :attribute mezőnek tartalmaznia kell a következő kulcsokat: :values.',
    'required_if' => 'A(z) :attribute kötelező, ha :other értéke ":value".',
    'required_if_accepted' => 'A(z) :attribute kötelező, ha :other el van fogadva.',
    'required_unless' => 'A(z) :attribute kötelező, kivéve, ha :other értéke ":values".',
    'required_with' => 'A(z) :attribute kötelező, ha :values meg van adva.',
    'required_with_all' => 'A(z) :attribute kötelező, ha minden :values mező ki van töltve.',
    'required_without' => 'A(z) :attribute kötelező, ha :values nincs megadva.',
    'required_without_all' => 'A(z) :attribute kötelező, ha egyik :values mező sincs megadva.',
    'same' => 'A(z) :attribute és :other mezőknek egyezniük kell.',
    'size' => [
        'array' => 'A(z) :attribute pontosan :size elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete :size kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute értéke pontosan :size kell legyen.',
        'string' => 'A(z) :attribute pontosan :size karakter hosszú kell legyen.',
    ],
    'starts_with' => 'A(z) :attribute a következők egyikével kell kezdődjön: :values.',
    'string' => 'A(z) :attribute szöveg kell legyen.',
    'timezone' => 'A(z) :attribute érvényes időzóna kell legyen.',
    'unique' => 'A(z) :attribute már foglalt.',
    'uploaded' => 'A(z) :attribute feltöltése sikertelen volt.',
    'uppercase' => 'A(z) :attribute csak nagybetűket tartalmazhat.',
    'url' => 'A(z) :attribute érvényes URL kell legyen.',
    'ulid' => 'A(z) :attribute érvényes ULID kell legyen.',
    'uuid' => 'A(z) :attribute érvényes UUID kell legyen.',

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
        'address' => 'Cím',
        'age' => 'Életkor',
        'body' => 'Tartalom',
        'cell' => 'Mobil',
        'city' => 'Város',
        'country' => 'Ország',
        'date' => 'Dátum',
        'day' => 'Nap',
        'excerpt' => 'Kivonat',
        'first_name' => 'Keresztnév',
        'gender' => 'Nem',
        'marital_status' => 'Családi állapot',
        'profession' => 'Foglalkozás',
        'nationality' => 'Állampolgárság',
        'hour' => 'Óra',
        'last_name' => 'Vezetéknév',
        'message' => 'Üzenet',
        'minute' => 'Perc',
        'mobile' => 'Mobiltelefonszám',
        'month' => 'Hónap',
        'name' => 'Név',
        'zipcode' => 'Irányítószám',
        'company_name' => 'Cégnév',
        'neighborhood' => 'Környék',
        'number' => 'Szám',
        'password' => 'Jelszó',
        'phone' => 'Telefonszám',
        'second' => 'Másodperc',
        'sex' => 'Nem',
        'state' => 'Megye / Tartomány',
        'street' => 'Utca',
        'subject' => 'Tárgy',
        'text' => 'Szöveg',
        'time' => 'Idő',
        'title' => 'Cím',
        'username' => 'Felhasználónév',
        'year' => 'Év',
        'description' => 'Leírás',
        'password_confirmation' => 'Jelszó megerősítése',
        'current_password' => 'Jelenlegi jelszó',
        'complement' => 'Kiegészítés',
        'modality' => 'Mód',
        'category' => 'Kategória',
        'blood_type' => 'Vércsoport',
        'birth_date' => 'Születési dátum',
    ],
];
