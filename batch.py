#!/usr/bin/python

from subprocess import call
from random import *

for i in range (0,1000):
    rand_seed = randrange( randrange(1,123), randrange(888,65777) )
    #print i+1, ": " , rand_seed
    call('./exchange_direct.php "Some seed - %d"' % rand_seed, shell=True)

