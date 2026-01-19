<?php

namespace App\Services\HBL;

class SecurityData
{
    /**
     * JWE Key Id. 
     *
     * @var string
     */
    public static string $EncryptionKeyId = "7664a2ed0dee4879bdfca0e8ce1ac313";

    /** 
     * Access Token.
     *
     * @var string
     */
    public static string $AccessToken = "65805a1636c74b8e8ac81a991da80be4";

    /**
     * Token Type - Used in JWS and JWE header.
     *
     * @var string
     */
    public static string $TokenType = "JWT";

    /**
     * JWS (JSON Web Signature) Signature Algorithm - This parameter identifies the cryptographic algorithm used to
     * secure the JWS.
     *
     * @var string
     */
    public static string $JWSAlgorithm = "PS256";

    /**
     * JWE (JSON Web Encryption) Key Encryption Algorithm - This parameter identifies the cryptographic algorithm
     * used to secure the JWE.
     *
     * @var string
     */
    public static string $JWEAlgorithm = "RSA-OAEP";

    /**
     * JWE (JSON Web Encryption) Content Encryption Algorithm - This parameter identifies the content encryption
     * algorithm used on the plaintext to produce the encrypted ciphertext.
     *
     * @var string
     */
    public static string $JWEEncrptionAlgorithm = "A128CBC-HS256";

    /**
     * Merchant Signing Private Key is used to cryptographically sign and create the request JWS.
     *
     * @var string
     */
    public static string $MerchantSigningPrivateKey = "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";
    /**
     * PACO Encryption Public Key is used to cryptographically encrypt and create the request JWE.
     *
     * @var string
     */
    public static string $PacoEncryptionPublicKey = "MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAviq4wrTmVMkRHouiHLUonJ1d6ss6nNreJ0JWpLwmTwAM7l35g8AFIvE8PqwWevtjuil9JZ1T1zwQTP8aM3s5/RzX5yFIhec/O14jib7Nmi4jACeJqDlHsnYzeCPw8WOhgmxWKHcORNLpn68jgnhLrKwh3Mooz/hXtIwGuNe/pYU7i/QaiuOjtmIcQ3yxJWjiHsllaogobZjbwMzwhp1fJ6ELmZp0FJvDrE8dn4UU9yzPFNzQ4gJzJAS/JKLXjfDw5mDQdw80vbzYuxksU0bc/3+DwY6hqaVJsP2AST7dCTR1wYzevzPxp0HMDmz1Ia/hSrmTPRhSa0qvxHMriVHUJvJeLTNI3cWM0RI9ukR7/v6vcf8ZwOZ+u7w4YfLpPCQFN7zGUN9Hho0pWBVYOstqsF5h/ZgBOlEHgSYY3CJdscV1+vKUvmFPiwkOdVxhc571RX56o+V71ZIGjXeYeqd3KNnND1JNsOn4hRPbk8Cl0e8CfZnEePfqtbFQGrzRU3GvSXscMb51TlvZu9i0toJdIJ4DiOCkUlB2sDI4x7N9ROOEbAD8uv68/jZqTM2paUNRN7Xvaa2LUCis3acadiyLt0tpuOT0sY2OejhLJshwNfTfc67gdtCJ3diddZWkXYpBgkMhuVj3TSx85sUklbGGJkzkwNsC0JhMSo7ZqbYxczECAwEAAQ==";
    /**
     * PACO Signing Public Key is used to cryptographically verify the response JWS signature.
     *
     * @var string
     */
    public static string $PacoSigningPublicKey = "MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAkEOCDQxCbyv/n1jadyDDL9KLRddF7W2eVNf7GwVeqlq3CVor0QHiU+yweO3b622NZAPDBy/GFeJJH5lwdJUbYojFWtHUqYN7/HoTHF50KhAbLMhnllsULuyVgG1l3m9xSjRJtQSaIZP5jF4LSM+m69Xd7U2qoTczMOaNZ36yWZzxN/OUQMjb2cWeZCLhVPf6zJwA35kC57NK2n1DDvvyFvLnh9gBd8EOkJuT9us1r01Ya3XpFHhXy1fTg9bmWXDMwMm5stnhmGOF2d6Uv4rYGqk67nRzX0ZEGrWW6X0tzeQESkQShx0algKIXeM/2RBfit1QHDHhI70CYTqt1eG05Cpr5u7FdvD4pk8fqfW8xJsmoZisQNQnov0oriUqrB1wZvWL8+calfoX0nxWMVlP37LspA6O2+dlnjFxpDQSjnfWVFyS6fKvr8jXWI6KG6L11J+yAXY4KjqGK+wEnH2yf8tK8NLkIAWNstlUQrycEkk4mP6ElKwkOMpRND0ArG1cG0uMx+VXd1vrWG6UePa+GHmgHbgLSkjI3hpz3wbpE5cbp73dbIgryeC0AeLY7kKDt7pMQpkg3gNxcvTGXjZYc1TQ5siuD1RBJUR5Lv/P8jjyQnB4D67AEuL1pw5acKQ3tfOEF+iuzzzV5zeSj5T5rYR1GpuPOqTz97AWSxawDUsCAwEAAQ==";
    /**
     * Merchant Decryption Private Key used to cryptographically decrypt the response JWE.
     * @var string
     */
    public static string $MerchantDecryptionPrivateKey = "MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAlcsc8ArgNAMxgQOgCPc+8obaDMnm3Jxv0Z23oXAZFQjGCV7gtxrcbtodIaOuutICvIZfIE52WI37ZCfISjyFJOX9wZoW337Q201ZoDR39Jxhyh0QU7ikQI8PMaRUTWr7RLXIM6hjgIZD9khjaM+f8g5PMtHnZkkvvkfGj65eObgt2JW0x7HfGdX8s44wwhvSeieoICzSE0amCaKfXIrVOhsVg/Nls/1L4pt9x3sqQgKXKy1fRfzQKzvivAhclvmIhN2FJAX1S+LGSAcG+GF640vkXY2dD+cLydQtM8m4hObLIJ/R1Av7SHvFak3k3OxQIWHZSWs++6G9P8rNqueCSYaAA5z9d6muUzXoIi3V40Nn3KryE7KrRfeHA3dix/IaMnXtomcskr2Nt0g6wnYKy1L8xO6Sn1gEo6nZj7tTCXHBveHd7lV7HXAYyhFMpk4KomIUv/gFFSUn6Nozn96FHE7te+O6NsrfFLDhaBmEvSfjZXoXMbxsyjY/p8eruV6v/nAvPDtH3cmg9MIhj4hWFUoLyfm+h8oLoqq1o1dOiG54JdjBSiVMDV5yjGtEDJbPV8230w5uSf0q3QVAryRW6BRxfS7LpBy226O7+BZBo7wHE2AcpVHWkZ6FJSmhmb9Ykq277x29j8Q9YnrAwO55b70OpIRgU66ep4FBiHgmXZcCAwEAAQ==";
}