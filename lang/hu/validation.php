<?php

return [

    'accepted' => 'A(z) :attribute elfogadása kötelező.',
    'accepted_if' => 'A(z) :attribute elfogadása kötelező, ha :other értéke :value.',
    'active_url' => 'A(z) :attribute nem egy érvényes URL.',
    'after' => 'A(z) :attribute dátuma :date utáni kell legyen.',
    'after_or_equal' => 'A(z) :attribute dátuma :date utáni vagy azzal egyenlő kell legyen.',
    'alpha' => 'A(z) :attribute csak betűket tartalmazhat.',
    'alpha_dash' => 'A(z) :attribute csak betűket, számokat, kötőjeleket és aláhúzásjeleket tartalmazhat.',
    'alpha_num' => 'A(z) :attribute csak betűket és számokat tartalmazhat.',
    'any_of' => 'A(z) :attribute érvénytelen.',
    'array' => 'A(z) :attribute tömb kell legyen.',
    'ascii' => 'A(z) :attribute csak egybájtos alfanumerikus karaktereket és szimbólumokat tartalmazhat.',
    'before' => 'A(z) :attribute dátuma :date előtti kell legyen.',
    'before_or_equal' => 'A(z) :attribute dátuma :date előtti vagy azzal egyenlő kell legyen.',

    'between' => [
        'array' => 'A(z) :attribute eleminek száma :min és :max között kell legyen.',
        'file' => 'A(z) :attribute mérete :min és :max kilobájt között kell legyen.',
        'numeric' => 'A(z) :attribute értéke :min és :max között kell legyen.',
        'string' => 'A(z) :attribute mezőnek minimum :min és maximum :max karakter között kell legyen.',
    ],

    'boolean' => 'A(z) :attribute mező értéke igaz vagy hamis kell legyen.',
    'can' => 'A(z) :attribute mező nem engedélyezett értéket tartalmaz.',
    'confirmed' => 'A(z) :attribute megerősítése nem egyezik.',
    'contains' => 'A(z) :attribute mezőből hiányzik egy kötelező érték.',
    'current_password' => 'A megadott jelszó helytelen.',
    'date' => 'A(z) :attribute nem érvényes dátum.',
    'date_equals' => 'A(z) :attribute dátuma meg kell egyezzen :date értékével.',
    'date_format' => 'A(z) :attribute formátuma nem egyezik a következővel: :format.',
    'decimal' => 'A(z) :attribute mezőnek :decimal tizedesjegyet kell tartalmaznia.',
    'declined' => 'A(z) :attribute mezőt el kell utasítani.',
    'declined_if' => 'A(z) :attribute mezőt el kell utasítani, ha :other értéke :value.',
    'different' => 'A(z) :attribute és :other mezőknek különbözniük kell.',
    'digits' => 'A(z) :attribute pontosan :digits számjegyből kell álljon.',
    'digits_between' => 'A(z) :attribute :min és :max számjegy között kell legyen.',
    'dimensions' => 'A(z) :attribute képméretei érvénytelenek.',
    'distinct' => 'A(z) :attribute mező ismétlődő értéket tartalmaz.',
    'doesnt_contain' => 'A(z) :attribute nem tartalmazhatja a következőket: :values.',
    'doesnt_end_with' => 'A(z) :attribute nem végződhet a következőkkel: :values.',
    'doesnt_start_with' => 'A(z) :attribute nem kezdődhet a következőkkel: :values.',
    'email' => 'A(z) :attribute mezőnek érvényes e-mail címnek kell lennie.',
    'encoding' => 'A(z) :attribute mező kódolásának :encoding értékűnek kell lennie.',
    'ends_with' => 'A(z) :attribute mezőnek a következők egyikével kell végződnie: :values.',
    'enum' => 'A kiválasztott :attribute érvénytelen.',
    'exists' => 'A kiválasztott :attribute érvénytelen.',
    'extensions' => 'A(z) :attribute fájl kiterjesztése csak a következő lehet: :values.',
    'file' => 'A(z) :attribute fájl kell legyen.',
    'filled' => 'A(z) :attribute mezőt ki kell tölteni.',

    'gt' => [
        'array' => 'A(z) :attribute több mint :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete nagyobb kell legyen mint :value kilobájt.',
        'numeric' => 'A(z) :attribute nagyobb kell legyen mint :value.',
        'string' => 'A(z) :attribute hossza nagyobb kell legyen mint :value karakter.',
    ],

    'gte' => [
        'array' => 'A(z) :attribute legalább :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete legalább :value kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute értéke legalább :value kell legyen.',
        'string' => 'A(z) :attribute hossza legalább :value karakter kell legyen.',
    ],

    'hex_color' => 'A(z) :attribute mezőnek érvényes hexadecimális színkódnak kell lennie.',
    'image' => 'A(z) :attribute mezőnek képnek kell lennie.',
    'in' => 'A kiválasztott :attribute érvénytelen.',
    'in_array' => 'A(z) :attribute mezőnek léteznie kell :other mezőben.',
    'integer' => 'A(z) :attribute mezőnek egész számnak kell lennie.',
    'ip' => 'A(z) :attribute mezőnek érvényes IP-címnek kell lennie.',
    'ipv4' => 'A(z) :attribute mezőnek érvényes IPv4-címnek kell lennie.',
    'ipv6' => 'A(z) :attribute mezőnek érvényes IPv6-címnek kell lennie.',
    'json' => 'A(z) :attribute mezőnek érvényes JSON-nak kell lennie.',
    'lowercase' => 'A(z) :attribute mező csak kisbetűket tartalmazhat.',

    'max' => [
        'array' => 'A(z) :attribute nem tartalmazhat több mint :max elemet.',
        'file' => 'A(z) :attribute nem lehet nagyobb mint :max kilobájt.',
        'numeric' => 'A(z) :attribute nem lehet nagyobb mint :max.',
        'string' => 'A(z) :attribute nem lehet hosszabb mint :max karakter.',
    ],

    'min' => [
        'array' => 'A(z) :attribute mezőnek legalább :min elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mezőnek legalább :min kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute mezőnek értéke legalább :min kell legyen.',
        'string' => 'A(z) :attribute mezőnek legalább :min karakter hosszúnak kell lennie.',
    ],

    'numeric' => 'A(z) :attribute mezőnek számnak kell lennie.',
    'required' => 'A(z) :attribute mező kitöltése kötelező.',
    'same' => 'A(z) :attribute mezőnek meg kell egyeznie a(z) :other mezővel.',
    'string' => 'A(z) :attribute mezőnek szövegnek kell lennie.',
    'unique' => 'A(z) :attribute már foglalt.',
    'url' => 'A(z) :attribute mezőnek érvényes URL-nek kell lennie.',

    'password' => [
        'letters' => 'A(z) :attribute mezőnek tartalmaznia kell legalább egy betűt.',
        'mixed' => 'A(z) :attribute mezőnek tartalmaznia kell kis- és nagybetűt is.',
        'numbers' => 'A(z) :attribute mezőnek tartalmaznia kell legalább egy számot.',
        'symbols' => 'A(z) :attribute mezőnek tartalmaznia kell legalább egy speciális karaktert.',
        'uncompromised' => 'A megadott :attribute szerepelt egy adatszivárgásban. Kérlek válassz másikat.',
    ],

    'attributes' => [
        'password' => 'jelszó',
        'email' => 'email',
        'name' => 'név',
        'acceptTerms' => 'Általános Szerződési Feltétel'
    ],

];
