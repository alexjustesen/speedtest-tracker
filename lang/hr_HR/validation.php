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

    'accepted' => 'Polje :attribute mora biti prihvaćeno.',
    'accepted_if' => 'Polje :attribute mora biti prihvaćeno kada je :other jednako :value.',
    'active_url' => 'Polje :attribute nije valjani URL.',
    'after' => 'Polje :attribute mora biti datum nakon :date.',
    'after_or_equal' => 'Polje :attribute mora biti datum jednak ili nakon :date.',
    'alpha' => 'Polje :attribute može sadržavati samo slova.',
    'alpha_dash' => 'Polje :attribute može sadržavati samo slova, brojeve, crtice i donje crte.',
    'alpha_num' => 'Polje :attribute može sadržavati samo slova i brojeve.',
    'array' => 'Polje :attribute mora biti niz.',
    'ascii' => 'Polje :attribute može sadržavati samo ASCII znakove.',
    'before' => 'Polje :attribute mora biti datum prije :date.',
    'before_or_equal' => 'Polje :attribute mora biti datum jednak ili prije :date.',
    'between' => [
        'array' => 'Polje :attribute mora imati između :min i :max stavki.',
        'file' => 'Polje :attribute mora biti između :min i :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti između :min i :max.',
        'string' => 'Polje :attribute mora imati između :min i :max znakova.',
    ],
    'boolean' => 'Polje :attribute mora biti istina ili laž.',
    'can' => 'Polje :attribute sadrži nevažeću vrijednost.',
    'confirmed' => 'Potvrda polja :attribute se ne podudara.',
    'current_password' => 'Unesena lozinka nije točna.',
    'date' => 'Polje :attribute nije valjani datum.',
    'date_equals' => 'Polje :attribute mora biti datum jednak :date.',
    'date_format' => 'Polje :attribute ne odgovara formatu :format.',
    'decimal' => 'Polje :attribute mora imati :decimal decimalnih mjesta.',
    'declined' => 'Polje :attribute mora biti odbijeno.',
    'declined_if' => 'Polje :attribute mora biti odbijeno kada je :other jednako ":value".',
    'different' => 'Polja :attribute i :other moraju biti različita.',
    'digits' => 'Polje :attribute mora imati :digits znamenki.',
    'digits_between' => 'Polje :attribute mora imati između :min i :max znamenki.',
    'dimensions' => 'Polje :attribute ima nevažeće dimenzije slike.',
    'distinct' => 'Polje :attribute ima dupliciranu vrijednost.',
    'doesnt_end_with' => 'Polje :attribute ne smije završavati sa: :values.',
    'doesnt_start_with' => 'Polje :attribute ne smije počinjati sa: :values.',
    'email' => 'Polje :attribute mora biti valjana email adresa.',
    'ends_with' => 'Polje :attribute mora završavati s jednom od sljedećih vrijednosti: :values.',
    'enum' => 'Odabrano polje :attribute nije važeće.',
    'exists' => 'Odabrano polje :attribute već postoji.',
    'file' => 'Polje :attribute mora biti datoteka.',
    'filled' => 'Polje :attribute mora imati vrijednost.',
    'gt' => [
        'array' => 'Polje :attribute mora imati više od :value stavki.',
        'file' => 'Polje :attribute mora biti veće od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće od :value.',
        'string' => 'Polje :attribute mora biti dulje od :value znakova.',
    ],
    'gte' => [
        'array' => 'Polje :attribute mora imati najmanje :value stavki.',
        'file' => 'Polje :attribute mora biti najmanje :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti najmanje :value.',
        'string' => 'Polje :attribute mora imati najmanje :value znakova.',
    ],
    'image' => 'Polje :attribute mora biti slika.',
    'in' => 'Odabrano polje :attribute nije važeće.',
    'in_array' => 'Polje :attribute ne postoji u :other.',
    'integer' => 'Polje :attribute mora biti cijeli broj.',
    'ip' => 'Polje :attribute mora biti valjana IP adresa.',
    'ipv4' => 'Polje :attribute mora biti valjana IPv4 adresa.',
    'ipv6' => 'Polje :attribute mora biti valjana IPv6 adresa.',
    'json' => 'Polje :attribute mora biti valjani JSON.',
    'lowercase' => 'Polje :attribute može sadržavati samo mala slova.',
    'lt' => [
        'array' => 'Polje :attribute mora imati manje od :value stavki.',
        'file' => 'Polje :attribute mora biti manje od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od :value.',
        'string' => 'Polje :attribute mora biti kraće od :value znakova.',
    ],
    'lte' => [
        'array' => 'Polje :attribute ne smije imati više od :value stavki.',
        'file' => 'Polje :attribute ne smije biti veće od :value kilobajta.',
        'numeric' => 'Polje :attribute ne smije biti veće od :value.',
        'string' => 'Polje :attribute ne smije biti duže od :value znakova.',
    ],
    'mac_address' => 'Polje :attribute mora biti valjana MAC adresa.',
    'max' => [
        'array' => 'Polje :attribute ne smije imati više od :max stavki.',
        'file' => 'Polje :attribute ne smije biti veće od :max kilobajta.',
        'numeric' => 'Polje :attribute ne smije biti veće od :max.',
        'string' => 'Polje :attribute ne smije biti duže od :max znakova.',
    ],
    'max_digits' => 'Polje :attribute ne smije imati više od :max znamenki.',
    'mimes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'min' => [
        'array' => 'Polje :attribute mora imati najmanje :min stavki.',
        'file' => 'Polje :attribute mora biti najmanje :min kilobajta.',
        'numeric' => 'Polje :attribute mora biti najmanje :min.',
        'string' => 'Polje :attribute mora imati najmanje :min znakova.',
    ],
    'min_digits' => 'Polje :attribute mora imati najmanje :min znamenki.',
    'missing' => 'Polje :attribute mora biti odsutno.',
    'missing_if' => 'Polje :attribute mora biti odsutno kada je :other jednako ":value".',
    'missing_unless' => 'Polje :attribute mora biti odsutno osim ako je :other jednako ":value".',
    'missing_with' => 'Polje :attribute mora biti odsutno kada je prisutno :values.',
    'missing_with_all' => 'Polje :attribute mora biti odsutno kada su prisutna sva polja :values.',
    'multiple_of' => 'Polje :attribute mora biti višekratnik od :value.',
    'not_in' => 'Odabrano polje :attribute nije važeće.',
    'not_regex' => 'Format polja :attribute nije valjan.',
    'numeric' => 'Polje :attribute mora biti broj.',
    'password' => [
        'letters' => 'Polje :attribute mora sadržavati barem jedno slovo.',
        'mixed' => 'Polje :attribute mora sadržavati barem jedno veliko i jedno malo slovo.',
        'numbers' => 'Polje :attribute mora sadržavati barem jedan broj.',
        'symbols' => 'Polje :attribute mora sadržavati barem jedan simbol.',
        'uncompromised' => 'Polje :attribute je kompromitirano u curenju podataka. Molimo odaberite drugo :attribute.',
    ],
    'present' => 'Polje :attribute mora biti prisutno.',
    'prohibited' => 'Polje :attribute je zabranjeno.',
    'prohibited_if' => 'Polje :attribute je zabranjeno kada je :other jednako ":value".',
    'prohibited_unless' => 'Polje :attribute je zabranjeno osim ako je :other jednako ":values".',
    'prohibits' => 'Polje :attribute zabranjuje postojanje polja :other.',
    'regex' => 'Format polja :attribute nije valjan.',
    'required' => 'Polje :attribute je obavezno.',
    'required_array_keys' => 'Polje :attribute mora sadržavati ključeve: :values.',
    'required_if' => 'Polje :attribute je obavezno kada je :other jednako ":value".',
    'required_if_accepted' => 'Polje :attribute je obavezno kada je :other prihvaćeno.',
    'required_unless' => 'Polje :attribute je obavezno osim ako je :other jednako ":values".',
    'required_with' => 'Polje :attribute je obavezno kada je prisutno :values.',
    'required_with_all' => 'Polje :attribute je obavezno kada su prisutna sva polja :values.',
    'required_without' => 'Polje :attribute je obavezno kada nije prisutno :values.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno od polja :values nije prisutno.',
    'same' => 'Polja :attribute i :other se moraju podudarati.',
    'size' => [
        'array' => 'Polje :attribute mora sadržavati točno :size stavki.',
        'file' => 'Polje :attribute mora biti veličine :size kilobajta.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string' => 'Polje :attribute mora imati :size znakova.',
    ],
    'starts_with' => 'Polje :attribute mora početi s jednom od sljedećih vrijednosti: :values.',
    'string' => 'Polje :attribute mora biti tekst.',
    'timezone' => 'Polje :attribute mora biti važeća vremenska zona.',
    'unique' => 'Polje :attribute već postoji.',
    'uploaded' => 'Učitavanje polja :attribute nije uspjelo.',
    'uppercase' => 'Polje :attribute može sadržavati samo velika slova.',
    'url' => 'Polje :attribute mora biti valjani URL.',
    'ulid' => 'Polje :attribute mora biti valjani ULID.',
    'uuid' => 'Polje :attribute mora biti valjani UUID.',

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
        'address' => 'Adresa',
        'age' => 'Dob',
        'body' => 'Sadržaj',
        'cell' => 'Mobitel',
        'city' => 'Grad',
        'country' => 'Država',
        'date' => 'Datum',
        'day' => 'Dan',
        'excerpt' => 'Izvadak',
        'first_name' => 'Ime',
        'gender' => 'Spol',
        'marital_status' => 'Bračni status',
        'profession' => 'Zanimanje',
        'nationality' => 'Nacionalnost',
        'hour' => 'Sat',
        'last_name' => 'Prezime',
        'message' => 'Poruka',
        'minute' => 'Minuta',
        'mobile' => 'Broj mobitela',
        'month' => 'Mjesec',
        'name' => 'Ime',
        'zipcode' => 'Poštanski broj',
        'company_name' => 'Naziv tvrtke',
        'neighborhood' => 'Kvart',
        'number' => 'Broj',
        'password' => 'Lozinka',
        'phone' => 'Telefon',
        'second' => 'Sekunda',
        'sex' => 'Spol',
        'state' => 'Županija / Pokrajina',
        'street' => 'Ulica',
        'subject' => 'Predmet',
        'text' => 'Tekst',
        'time' => 'Vrijeme',
        'title' => 'Naslov',
        'username' => 'Korisničko ime',
        'year' => 'Godina',
        'description' => 'Opis',
        'password_confirmation' => 'Potvrda lozinke',
        'current_password' => 'Trenutna lozinka',
        'complement' => 'Dodatak',
        'modality' => 'Mod',
        'category' => 'Kategorija',
        'blood_type' => 'Krvna grupa',
        'birth_date' => 'Datum rođenja',
    ],
];
