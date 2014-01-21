#!/usr/bin/python

from subprocess import call
from random import *

#logTypes = ['application.info', 'application.error', 'application.warning', 'system.error', 'system.warning', 'event.hit', 'event.view', 'event.share']
logTypes = ['application.info', 'application.error', 'system.error', 'system.info']

for i in range (0,10000):
    rand_seed = randrange( randrange(1,123), randrange(888,65777) )
    #print i+1, ": " , rand_seed
    #print choice(logTypes)
    #print './publisher.topic.php "%s" "Some seed - %d"' % (choice(logTypes), rand_seed)
    call('./publisher.topic.php "%s" "Some seed - %d"' % (choice(logTypes), rand_seed), shell=True)

