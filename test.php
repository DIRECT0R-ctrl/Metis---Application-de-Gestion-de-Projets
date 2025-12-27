<?php

require_once __DIR__ . '/src/repository/memberRepository.php';
require_once __DIR__ . '/src/entity/member.php';

$repo = new MemberRepository();

$members = $repo->findAll();
var_dump($members);
