import re
import time
import resource

t = time.monotonic()

max_iter = 50000
tenth_iter = max_iter / 10

def decor(fn):
	def _inner():
		print("(", end='')
		fn()
		print(")", end='')

	return _inner

def bench_function_calls():
	adder = lambda x, y: x + y

	result = -1024
	c = 0
	while c < max_iter:
		result = result + adder(c, 1)
		c = c + 1
		if c % tenth_iter == 0:
			print(':', end='')

	return c


def bench_regex_matches():

	haystack = "Když začínáme myslet, nemáme k dispozici nic jiného než myšlenku v " + \
		"její čisté neurčenosti, neboť k určení již patří jedno nebo nějaké " + \
		"jiné, ale na začátku ještě nemáme žádné jiné..."

	regex = re.compile("^.*(zač).*(,)?.*?(\.)")

	result = 0
	c = 0
	while c < max_iter:
		result = result + int(bool(regex.match(haystack)))
		c = c + 1
		if c % tenth_iter == 0:
			print(':', end='')

	return c

def bench_dicts():

	c = 0
	result = []

	while c < max_iter:

		dict_ = {
			'a': True,
			'b': False,
			'c': None,
			'd': 'áhojky, plantážníku!',
			'keys': ['a', 'b', 'c'],
		}

		for name in dict_['keys']:
			result.append(dict_[name])

		c = c + 1
		if c % tenth_iter == 0:
			print(':', end='')

	return result

decor(bench_function_calls)()
decor(bench_regex_matches)()
decor(bench_dicts)()

mempeak = resource.getrusage(resource.RUSAGE_SELF).ru_maxrss
print()
print(f"{time.monotonic() - t}\n{mempeak / 1_000}")
