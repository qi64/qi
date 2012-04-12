describe "esort", ->
  fixture = '#esort-ul-fixture'
  shouldBeSorted = (a = 'a', c = 'c')->
    expect($(fixture).find 'li:first').toHaveText a
    expect($(fixture).find('li').not(':first').not(':last').text()).toEqual ' B'
    expect($(fixture).find 'li:last' ).toHaveText c

  beforeEach ->
    loadFixtures "esort.fixture.html"

  it "Should Sort children and trim spaces and ignore case", ->
    $(fixture).esort()
    shouldBeSorted()

  it "Should Sort Reverse", ->
    $(fixture).esort reverse:-1
    shouldBeSorted('c', 'a')

  it "Should Chain after reverse", ->
    expect( $(fixture).esort() ).toBe fixture

  it "should accept custom attr", ->
    $(fixture).esort
      attr: ($el)-> $el.data('index')
    expect($(fixture).find 'li:first').toHaveText 'c'
    expect($(fixture).find 'li:last').toHaveText ' B'
