# Madlines Validation
Independent and elastic data filtering and validating library.

[![Build Status](https://travis-ci.org/Madlines/Validation.svg?branch=master)](https://travis-ci.org/Madlines/Validation)

## How does it work?
Simple!
You tell that what kind of data you expect.
It gets assoc array of input data. It filters it. It verifies it.
It returns filtered data for you to use or throws an exception with error messages inside.
If there will be some input data that is not specified in validation object - it will be filtered out.
If there will be some field defined in validation but missing in input data - it will be assumed to be null (by default).
It also let you verify stuff outside the validator and then integrate error message into ValidationExceptioni's messages.

## Why Exception?

Some may say that Exceptions shouldn't be used that way, but I see one big advantage of such approach.
I personally prefer to have a single controller to handle all kind of errors (Runtime, 404s, 401s and so on).
That way I don't have to handle Validation Error in every single data-storing related action.
Using exceptions it's easy - I can catch it as every other exception, pass it to error controller and handle
it in there.

## Installation

You could of course just get files from github but then you would have to deal with autoloading by yourself.
Prefered and much more comfortable way is to use composer (it's not on packagist just yet).
Add that into your `composer.json` file:

```
{                                                                                                                                                                                                              
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Madlines/Validation.git"
        }
    ],
    "require": {
        "madlines/validation": "dev-master"
    }
}

```

then open terminal, navigate to your project's directory and type
`composer install`.

## How to use
### Without separate config array

```
<?php                                                                                                                                                                                                          
require 'vendor/autoload.php';                                               
                                                                               
$validation = new \Madlines\Validation\Validation();                                                   
                                                                                                       
$validation->field('aNumber')                                                                          
    ->addFilter('int');                                                                                
                                                                                                       
$validation->field('email')                                                                            
    ->required('Field :fied is required')     
    ->addRule('email', 'Field :field has to be a valid email address');                                
                                                                                                       
$validation->field('password')                                                                         
    ->required('Field :fied is required')                                                              
    ->addRule(                                                                                         
        'len',                                                                                         
        'Field :field has to be :min to :max characters long',                                         
        [                                                                                              
            'min' => 8,                                                                                
            'max' => 30                                                                                
        ]                                                                                              
    );                                                                                                 
                                                                                                       
$data = [];                                                                                            
$data['email'] = 'notemail';                                                                           
$data['password'] = 'short';                                                                           
$data['aNumber'] = '3';                                                                                
                                                                                                       
try {                                                                                                  
    $filtered = $validation->execute($data);                                                           
    // use data, e.g. save it to db                                                                    
} catch (\Madlines\Validation\ValidationException $e) {                                                
    $messages = $e->getErrorMessages();                                                                
    // display messages or do whatever you need with it                                                
} 
```

### With separate config array

Previous example is pretty elegant and clean, but validation definitions can be very long.
Because of that you should keep your validation config somewhere else. It doesn't limit you
in any way, because between creating Validation object and executing validation process
you can do whatever you want. See example:

```
<?php                                                                                                                                                                                                          
require 'vendor/autoload.php';

$config = [ 
    'aNumber' => [
        'default' => 18,
        'filters' => ['int'],
    ],  
    'email' => [
        'required' => 'Field :field is required',
        'rules' => [
            'email' => ['Field :field has to be a valid email address', []] 
        ],  
    ],  
    'password' => [
        'required' => 'Field :field is required',
        'rules' => [
            'len' => [
                'Field :field has to be :min to :max characters long',
                [   
                    'min' => 8,
                    'max' => 30
                ]   
            ]   
        ],  
    ]   
];

$validation = new \Madlines\Validation\Validation($config);

$data = []; 
$data['email'] = 'notemail';
$data['password'] = 'short';

// do some additional checking outside the validator
if (email_taken($data['email'])) {
    $validation->field('email')->forceError('User with such email address is already registered');
}


try {
    $filtered = $validation->execute($data);
    // use data, e.g. save it to db
} catch (\Madlines\Validation\ValidationException $e) {
    $messages = $e->getErrorMessages();
    // display messages or do whatever you need with it
}

```

### Forcing errors

Sometimes you need to do some additional checking outside the Validator, like e.g. verifying if
requested email address is free. In such cases method `forceError` comes in handy.
As you could have seen on previous code block - we verified just that using some external function.
After that we forced field `email` to report an error on itself using method `forceError`. That way
your forced error message will end up with all other error messages inside ValidationException.

## Setting default values

If some field that you defined in your validator doesn't exist inside the input it is assumed to be null.
However you might need/want to have different default value. You can achieve that by using method `setDefault`
on your Field `object`. Alternatively you can add key `default` to your configuration array (as shown on previous listing).

```
$validation->field('foo')->setDefault('bar');
```

## Contributing

You thing something can be improved? You're probably right :)
Feel free to contribute.
